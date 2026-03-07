<?php

namespace Tests\Unit;

use App\Models\SavedDesign;
use App\Models\ProductType;
use App\Models\User;
use App\Services\Design\ExportService;
use App\Jobs\GenerateDesignPreview;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DesignServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_export_design_to_high_res()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $productType = ProductType::factory()->create();

        $design = SavedDesign::factory()->create([
            'user_id' => $user->id,
            'product_type_id' => $productType->id,
        ]);

        $exportService = new ExportService();
        
        $result = $exportService->exportHighRes($design, 2); // 2x multiplier

        $this->assertNotNull($result);
        $this->assertStringContainsString('design_' . $design->id . '_highres.png', $result);
        
        // Check that file exists in storage
        $path = str_replace('/storage/', 'public/', parse_url($result, PHP_URL_PATH));
        Storage::assertExists($path);
    }

    /** @test */
    public function it_can_generate_preview()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $productType = ProductType::factory()->create();

        $design = SavedDesign::factory()->create([
            'user_id' => $user->id,
            'product_type_id' => $productType->id,
        ]);

        $exportService = new ExportService();
        
        $result = $exportService->generatePreview($design);

        $this->assertNotNull($result);
        $this->assertStringContainsString('design_' . $design->id . '_preview.jpg', $result);
        
        // Check that file exists in storage
        $path = str_replace('/storage/', 'public/', parse_url($result, PHP_URL_PATH));
        Storage::assertExists($path);
    }

    /** @test */
    public function it_handles_invalid_design_data()
    {
        $user = User::factory()->create();
        $productType = ProductType::factory()->create();

        $design = SavedDesign::factory()->create([
            'user_id' => $user->id,
            'product_type_id' => $productType->id,
            'design_data' => null, // Invalid data
        ]);

        $exportService = new ExportService();
        
        $this->expectException(\Exception::class);
        $exportService->exportHighRes($design);
    }

    /** @test */
    public function it_can_export_multiple_formats()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $productType = ProductType::factory()->create();

        $design = SavedDesign::factory()->create([
            'user_id' => $user->id,
            'product_type_id' => $productType->id,
        ]);

        $exportService = new ExportService();
        
        // Test high res export
        $highRes = $exportService->exportHighRes($design);
        $this->assertNotNull($highRes);
        
        // Test preview export
        $preview = $exportService->generatePreview($design);
        $this->assertNotNull($preview);
    }
}
