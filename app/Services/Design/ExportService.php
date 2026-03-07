<?php

namespace App\Services\Design;

use App\Models\SavedDesign;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class ExportService
{
    protected $manager;
    protected $dpi = 300;

    public function __construct()
    {
        $driver = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
        $this->manager = new ImageManager($driver);
    }

    /**
     * Export design to high-resolution image for printing
     */
    public function exportHighRes(SavedDesign $design, $multiplier = 4)
    {
        // This would typically use a headless browser or canvas rendering
        // For now, we'll create a placeholder implementation
        
        $canvasData = $design->design_data;
        
        if (!$canvasData) {
            throw new \Exception('Invalid design data');
        }
        
        // Calculate print dimensions (assuming base is screen resolution)
        $baseWidth = $canvasData['width'] ?? 800;
        $baseHeight = $canvasData['height'] ?? 800;
        
        $printWidth = $baseWidth * $multiplier;
        $printHeight = $baseHeight * $multiplier;
        
        // Create high-res canvas
        $image = $this->manager->create($printWidth, $printHeight)->fill('#ffffff');
        
        // Render each element from design_data
        // This is simplified - you'd need proper Fabric.js/Konva rendering
        if (isset($canvasData['objects'])) {
            foreach ($canvasData['objects'] as $object) {
                $this->renderElement($image, $object, $multiplier);
            }
        }
        
        // Save the file
        $filename = 'design_' . $design->id . '_highres.png';
        $path = 'exports/' . $design->product_type_id . '/' . $filename;
        
        $image->save(Storage::disk('public')->path($path));
        
        $url = Storage::disk('public')->url($path);
        
        // Update design with export URL
        $printFiles = $design->print_files ?? [];
        $printFiles['high_res'] = $url;
        $design->update(['print_files' => $printFiles]);
        
        return $url;
    }

    /**
     * Generate print-ready file for order item
     */
    public function generatePrintFile(SavedDesign $design)
    {
        $printArea = $design->productType->printAreas()->default()->first();
        
        if (!$printArea) {
            throw new \Exception('No default print area defined for this product type');
        }
        
        // Get required canvas size based on print area physical dimensions
        $requiredSize = $printArea->required_canvas_size;
        
        // Export at print resolution
        $multiplier = ceil($requiredSize['width_px'] / ($design->design_data['width'] ?? 800));
        
        return $this->exportHighRes($design, max($multiplier, 1));
    }

    /**
     * Generate preview image for web display
     */
    public function generatePreview(SavedDesign $design, $size = 800)
    {
        $canvasData = $design->design_data;
        
        if (!$canvasData) {
            return null;
        }
        
        // Create preview image
        $image = $this->manager->create($size, $size)->fill('#ffffff');
        
        // Render elements (simplified)
        if (isset($canvasData['objects'])) {
            foreach ($canvasData['objects'] as $object) {
                $this->renderElement($image, $object, $size / ($canvasData['width'] ?? 800));
            }
        }
        
        // Save preview
        $filename = 'design_' . $design->id . '_preview.jpg';
        $path = 'previews/' . $design->product_type_id . '/' . $filename;
        
        $image->toJpeg()->save(Storage::disk('public')->path($path), ['quality' => 85]);
        
        $url = Storage::disk('public')->url($path);
        
        // Update design
        $design->update(['preview_url' => $url]);
        
        return $url;
    }

    /**
     * Export design as PDF (for print shops)
     */
    public function exportToPdf(SavedDesign $design)
    {
        // This would require a PDF library like TCPDF or Dompdf
        // Implementation depends on specific print requirements
        
        $filename = 'design_' . $design->id . '.pdf';
        $path = 'pdf_exports/' . $filename;
        
        // Placeholder - implement with actual PDF generation
        // $pdf = new \TCPDF();
        // $pdf->AddPage();
        // $pdf->Image($this->exportHighRes($design), ...);
        // $pdf->Output(Storage::disk('public')->path($path), 'F');
        
        return Storage::disk('public')->url($path);
    }

    /**
     * Export design layers separately (for advanced editing)
     */
    public function exportLayers(SavedDesign $design)
    {
        $canvasData = $design->design_data;
        $layers = [];
        
        if (!isset($canvasData['objects'])) {
            return $layers;
        }
        
        $baseWidth = $canvasData['width'] ?? 800;
        $baseHeight = $canvasData['height'] ?? 800;
        
        foreach ($canvasData['objects'] as $index => $object) {
            $layerImage = $this->manager->create($baseWidth, $baseHeight)->fill('transparent');
            
            $this->renderElement($layerImage, $object, 1);
            
            $filename = "layer_{$index}_{$object['type']}.png";
            $path = "layers/{$design->id}/{$filename}";
            
            $layerImage->save(Storage::disk('public')->path($path));
            
            $layers[] = [
                'name' => $object['type'] ?? "Layer {$index}",
                'path' => $path,
                'url' => Storage::disk('public')->url($path),
            ];
        }
        
        return $layers;
    }

    /**
     * Render a single design element onto the canvas
     * This is a simplified implementation - you'd need full Fabric.js compatibility
     */
    protected function renderElement($image, array $element, $scale = 1)
    {
        $type = $element['type'] ?? null;
        
        if (!$type) {
            return;
        }
        
        $x = ($element['left'] ?? 0) * $scale;
        $y = ($element['top'] ?? 0) * $scale;
        
        switch ($type) {
            case 'i-text':
            case 'text':
                $text = $element['text'] ?? '';
                $fontSize = ($element['fontSize'] ?? 40) * $scale;
                $fillColor = $element['fill'] ?? '#000000';
                
                $image->text($text, $x, $y + $fontSize, function($font) use ($fontSize, $fillColor) {
                    $font->size($fontSize);
                    $font->color($fillColor);
                    // Add font family support
                });
                break;
                
            case 'image':
                if (isset($element['src'])) {
                    try {
                        $elementImage = $this->manager->read($element['src']);
                        
                        $width = ($element['width'] ?? 100) * $scale;
                        $height = ($element['height'] ?? 100) * $scale;
                        
                        $elementImage->resize($width, $height);
                        
                        $image->place($elementImage, anchor: 'top-left', offsetX: $x, offsetY: $y);
                    } catch (\Exception $e) {
                        \Log::error('Failed to render image element: ' . $e->getMessage());
                    }
                }
                break;
                
            case 'rect':
                $width = ($element['width'] ?? 100) * $scale;
                $height = ($element['height'] ?? 100) * $scale;
                $fill = $element['fill'] ?? '#000000';
                
                $image->rectangle($x, $y, $x + $width, $y + $height, $fill);
                break;
                
            case 'circle':
                $radius = (($element['radius'] ?? 50) * $scale);
                $fill = $element['fill'] ?? '#000000';
                
                $image->ellipse($x + $radius, $y + $radius, $radius * 2, $radius * 2, $fill);
                break;
        }
        
        // Handle rotation if needed
        if (isset($element['angle']) && $element['angle'] != 0) {
            $image->rotate($element['angle']);
        }
    }

    /**
     * Batch export multiple designs
     */
    public function batchExport(array $designIds, $format = 'high_res')
    {
        $results = [];
        
        foreach ($designIds as $designId) {
            $design = SavedDesign::find($designId);
            
            if (!$design) {
                continue;
            }
            
            try {
                switch ($format) {
                    case 'high_res':
                        $url = $this->exportHighRes($design);
                        break;
                    case 'preview':
                        $url = $this->generatePreview($design);
                        break;
                    case 'pdf':
                        $url = $this->exportToPdf($design);
                        break;
                    default:
                        $url = null;
                }
                
                $results[] = [
                    'design_id' => $designId,
                    'success' => true,
                    'url' => $url,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'design_id' => $designId,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return $results;
    }
}
