<?php

namespace App\Services;

use App\Models\DesignTemplate;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TemplateService
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Create a new template from an uploaded file
     *
     * @param array $data
     * @param UploadedFile $image
     * @param int|null $userId
     * @return DesignTemplate
     */
    public function createTemplate(array $data, UploadedFile $image, ?int $userId = null): DesignTemplate
    {
        // Validate the file
        if (!$this->fileService->validateFile($image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'], 10240)) {
            throw new \InvalidArgumentException('Invalid file type or size');
        }

        // Organize the template directory
        $organizedDirectory = $this->fileService->getOrganizedDirectory('template');

        // Upload the main image
        $uploadResult = $this->fileService->uploadFile($image, $organizedDirectory);

        // Create thumbnail
        $thumbnailUrl = $this->fileService->createThumbnail($uploadResult['file_path'], 300, 300, 'template/thumbnails');

        // Create the design template record
        return DesignTemplate::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'thumbnail_url' => $thumbnailUrl,
            'preview_url' => $uploadResult['file_url'],
            'design_data' => $data['design_data'] ?? [], // Can be populated with design elements
            'created_by' => $userId,
        ]);
    }

    /**
     * Update an existing template
     *
     * @param DesignTemplate $template
     * @param array $data
     * @param UploadedFile|null $image
     * @return DesignTemplate
     */
    public function updateTemplate(DesignTemplate $template, array $data, ?UploadedFile $image = null): DesignTemplate
    {
        $templateData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? $template->description,
            'category' => $data['category'] ?? $template->category,
        ];

        // Handle image update if provided
        if ($image) {
            // Validate the file
            if (!$this->fileService->validateFile($image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'], 10240)) {
                throw new \InvalidArgumentException('Invalid file type or size');
            }

            // Delete old files if they exist
            $this->deleteTemplateFiles($template);

            // Organize the template directory
            $organizedDirectory = $this->fileService->getOrganizedDirectory('template');

            // Upload the new image
            $uploadResult = $this->fileService->uploadFile($image, $organizedDirectory);

            // Create new thumbnail
            $thumbnailUrl = $this->fileService->createThumbnail($uploadResult['file_path'], 300, 300, 'template/thumbnails');

            // Add file URLs to template data
            $templateData['thumbnail_url'] = $thumbnailUrl;
            $templateData['preview_url'] = $uploadResult['file_url'];
        }

        $template->update($templateData);

        return $template;
    }

    /**
     * Delete template and its associated files
     *
     * @param DesignTemplate $template
     * @return bool
     */
    public function deleteTemplate(DesignTemplate $template): bool
    {
        // Delete associated files
        $this->deleteTemplateFiles($template);

        // Delete the template record
        return $template->delete();
    }

    /**
     * Delete template files from storage
     *
     * @param DesignTemplate $template
     * @return void
     */
    private function deleteTemplateFiles(DesignTemplate $template): void
    {
        if ($template->preview_url) {
            // Extract the file path from the URL
            $filePath = str_replace(Storage::url(''), '', $template->preview_url);
            // Remove the leading slash if present
            if (strpos($filePath, '/') === 0) {
                $filePath = substr($filePath, 1);
            }
            
            if (Storage::disk('public')->exists($filePath)) {
                $this->fileService->deleteFile($filePath);
            }
        }

        if ($template->thumbnail_url) {
            // Extract the thumbnail path from the URL
            $thumbnailPath = str_replace(Storage::url(''), '', $template->thumbnail_url);
            // Remove the leading slash if present
            if (strpos($thumbnailPath, '/') === 0) {
                $thumbnailPath = substr($thumbnailPath, 1);
            }
            
            if (Storage::disk('public')->exists($thumbnailPath)) {
                $this->fileService->deleteFile($thumbnailPath);
            }
        }
    }

    /**
     * Get templates with filtering options
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTemplates(array $filters = [], int $perPage = 20)
    {
        $query = DesignTemplate::query();

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('description', 'LIKE', "%{$filters['search']}%");
            });
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['user_id'])) {
            $query->where('created_by', $filters['user_id']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Increment template usage count
     *
     * @param DesignTemplate $template
     * @return void
     */
    public function incrementUsage(DesignTemplate $template): void
    {
        $template->increment('usage_count');
    }

    /**
     * Get popular templates
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPopularTemplates(int $limit = 10)
    {
        return DesignTemplate::orderByDesc('usage_count')
                             ->limit($limit)
                             ->get();
    }

    /**
     * Get templates by category
     *
     * @param string $category
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTemplatesByCategory(string $category, int $limit = 10)
    {
        return DesignTemplate::where('category', $category)
                             ->limit($limit)
                             ->get();
    }
}