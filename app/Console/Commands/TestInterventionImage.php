<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\Laravel\Facades\Image;

class TestInterventionImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:intervention-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Intervention Image functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Testing Intervention Image...');
            
            // Test with a local image file
            $imagePath = public_path('storage/products/coding_is_just/mainImage.png');
            if (file_exists($imagePath)) {
                $image = Image::read($imagePath);
                $this->info('Success! Image loaded with dimensions: ' . $image->width() . 'x' . $image->height());
            } else {
                $this->error('Image file not found: ' . $imagePath);
                return;
            }
            
            $this->info('Intervention Image is working correctly!');
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
        }
    }
}