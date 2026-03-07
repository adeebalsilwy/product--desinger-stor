<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesignTemplate;
use App\Services\TemplateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\Paginator;

class TemplatesController extends Controller
{
    protected $templateService;

    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search'),
            'category' => $request->input('category'),
        ];
        
        $templates = $this->templateService->getTemplates($filters, 20);
        
        return response()->json([
            'templates' => $templates->items(),
            'pagination' => [
                'current_page' => $templates->currentPage(),
                'last_page' => $templates->lastPage(),
                'per_page' => $templates->perPage(),
                'total' => $templates->total(),
                'from' => $templates->firstItem(),
                'to' => $templates->lastItem(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240', // 10MB max
        ]);

        try {
            $template = $this->templateService->createTemplate(
                $request->only(['name', 'description', 'category']),
                $request->file('image'),
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Template created successfully',
                'template' => $template
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(DesignTemplate $template)
    {
        try {
            $this->templateService->deleteTemplate($template);

            return response()->json([
                'success' => true,
                'message' => 'Template deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}