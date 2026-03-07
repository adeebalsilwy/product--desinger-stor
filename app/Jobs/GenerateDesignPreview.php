<?php

namespace App\Jobs;

use App\Models\SavedDesign;
use App\Services\Design\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateDesignPreview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $design;

    /**
     * Create a new job instance.
     */
    public function __construct(SavedDesign $design)
    {
        $this->design = $design;
    }

    /**
     * Execute the job.
     */
    public function handle(ExportService $exportService): void
    {
        try {
            Log::info("Generating preview for design #{$this->design->id}");
            
            // Generate web preview
            $previewUrl = $exportService->generatePreview($this->design);
            
            if ($previewUrl) {
                $this->design->update([
                    'preview_url' => $previewUrl,
                ]);
                
                Log::info("Preview generated successfully for design #{$this->design->id}: {$previewUrl}");
            }
            
            // Optionally generate thumbnail
            $thumbnailPath = $this->design->generateThumbnail();
            if ($thumbnailPath) {
                $this->design->update([
                    'thumbnail_url' => $thumbnailPath,
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error("Failed to generate preview for design #{$this->design->id}: " . $e->getMessage());
            
            // Fail gracefully - don't throw exception
            // The design is still usable without preview
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical("Design preview job failed for design #{$this->design->id}: " . $exception->getMessage());
        
        // Optionally notify admin or retry
    }
}
