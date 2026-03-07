<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAsset;
use App\Services\Image\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Upload a new asset (image, SVG, etc.)
     */
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'type' => 'nullable|string|in:image,svg,other',
        ]);
        
        $file = $validated['file'];
        
        // Validate file type
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            return response()->json([
                'error' => 'Invalid file type. Allowed: ' . implode(', ', $allowedMimeTypes),
            ], 422);
        }
        
        // Determine file type
        $fileType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'other';
        if ($file->getMimeType() === 'image/svg+xml') {
            $fileType = 'svg';
        }
        
        // Upload the file
        $uploadPath = 'user_assets/' . (Auth::id() ?? 'guest') . '/' . date('Y/m');
        
        try {
            $uploadResult = $this->imageService->upload($file, $uploadPath, [
                'thumbnail' => true,
                'thumbnail_size' => 300,
                'disk' => config('filesystems.default', 'public'),
            ]);
            
            // Create asset record
            $asset = UserAsset::create([
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId(),
                'original_filename' => $file->getClientOriginalName(),
                'stored_filename' => $uploadResult['filename'],
                'file_path' => $uploadResult['path'],
                'file_url' => $uploadResult['url'],
                'thumbnail_url' => $uploadResult['thumbnail_url'],
                'file_type' => $fileType,
                'file_size' => $uploadResult['size'],
                'mime_type' => $uploadResult['mime_type'],
                'width' => $uploadResult['dimensions']['width'] ?? null,
                'height' => $uploadResult['dimensions']['height'] ?? null,
                'storage_disk' => config('filesystems.default', 'public'),
            ]);
            
            return response()->json([
                'data' => $asset,
                'message' => 'File uploaded successfully',
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List user's assets
     */
    public function index(Request $request)
    {
        $query = UserAsset::query();
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('file_type', $request->type);
        }
        
        // Filter by search term
        if ($request->has('search')) {
            $query->where('original_filename', 'like', '%' . $request->search . '%');
        }
        
        // User's assets or guest assets by session
        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $request->session()->getId());
        }
        
        $assets = $query->latest()->paginate($request->get('per_page', 20));
        
        return response()->json([
            'data' => $assets->items(),
            'meta' => [
                'current_page' => $assets->currentPage(),
                'last_page' => $assets->lastPage(),
                'per_page' => $assets->perPage(),
                'total' => $assets->total(),
            ],
        ]);
    }

    /**
     * Get specific asset
     */
    public function show(UserAsset $asset)
    {
        // Check authorization
        if (!$this->canAccessAsset($asset)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        return response()->json([
            'data' => $asset,
        ]);
    }

    /**
     * Delete an asset
     */
    public function destroy(UserAsset $asset)
    {
        if (!$this->canAccessAsset($asset)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Check if asset is in use
        if ($asset->isInUse()) {
            return response()->json([
                'error' => 'Cannot delete asset. It is being used in one or more designs.',
            ], 422);
        }
        
        $asset->delete();
        
        return response()->json([
            'message' => 'Asset deleted successfully',
        ]);
    }

    /**
     * Bulk delete assets
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'asset_ids' => 'required|array',
            'asset_ids.*' => 'integer|exists:user_assets,id',
        ]);
        
        $deletedCount = 0;
        $failedIds = [];
        
        foreach ($validated['asset_ids'] as $assetId) {
            $asset = UserAsset::find($assetId);
            
            if ($asset && $this->canAccessAsset($asset) && !$asset->isInUse()) {
                $asset->delete();
                $deletedCount++;
            } else {
                $failedIds[] = $assetId;
            }
        }
        
        return response()->json([
            'deleted_count' => $deletedCount,
            'failed_ids' => $failedIds,
            'message' => "Successfully deleted {$deletedCount} assets",
        ]);
    }

    /**
     * Check if user can access an asset
     */
    protected function canAccessAsset(UserAsset $asset): bool
    {
        // Admin can access all
        if (Auth::check() && Auth::user()->role === 'admin') {
            return true;
        }
        
        // Owner can access
        if (Auth::check() && $asset->user_id === Auth::id()) {
            return true;
        }
        
        // Guest can access by session
        if (!Auth::check() && $asset->session_id === request()->session()->getId()) {
            return true;
        }
        
        return false;
    }
}
