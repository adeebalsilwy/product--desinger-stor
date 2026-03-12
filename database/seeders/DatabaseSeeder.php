<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\CustomersFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            GuestAdminSeeder::class,
            CustomerSeeder::class, 
            UserSeeder::class,
            ProductTypeSeeder::class,
            ProductSeeder::class,  // Added template-based products
            ProductImagesSeeder::class,  // Added images to products using template images
            TshirtSeeder::class,   // Kept for backward compatibility
            OrderSeeder::class,
            FontSeeder::class,
            ClipartSeeder::class,
            TemplateSeeder::class,
            CompletePermissionsSeeder::class,
        ]);
    }
}