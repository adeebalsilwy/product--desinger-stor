<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@dshirts.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '+1234567890',
                'address' => '123 Admin Street',
                'city' => 'Admin City',
                'country' => 'Admin Country',
                'zip_code' => '12345',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Staff User
        User::firstOrCreate(
            ['email' => 'staff@dshirts.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('staff123'),
                'role' => 'staff',
                'phone' => '+1234567891',
                'address' => '456 Staff Avenue',
                'city' => 'Staff City',
                'country' => 'Staff Country',
                'zip_code' => '67890',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Customer User
        User::firstOrCreate(
            ['email' => 'customer@dshirts.com'],
            [
                'name' => 'Customer User',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'phone' => '+1234567892',
                'address' => '789 Customer Road',
                'city' => 'Customer City',
                'country' => 'Customer Country',
                'zip_code' => '54321',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create additional sample customers
        $sampleCustomers = [
            [
                'name' => 'Ahmed Mohamed',
                'email' => 'ahmed@example.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'phone' => '+201234567890',
                'address' => '123 Cairo Street',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'zip_code' => '11511',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'phone' => '+14567890123',
                'address' => '456 New York Ave',
                'city' => 'New York',
                'country' => 'USA',
                'zip_code' => '10001',
            ],
            [
                'name' => 'Mohamed Ali',
                'email' => 'mohamed@example.com',
                'password' => Hash::make('customer123'),
                'role' => 'customer',
                'phone' => '+201001234567',
                'address' => '789 Alexandria St',
                'city' => 'Alexandria',
                'country' => 'Egypt',
                'zip_code' => '21500',
            ]
        ];

        foreach ($sampleCustomers as $customerData) {
            User::firstOrCreate(
                ['email' => $customerData['email']],
                array_merge($customerData, [
                    'is_active' => true,
                    'email_verified_at' => now(),
                ])
            );
        }

        $this->command->info('Sample users created successfully!');
        $this->command->info("\n=== Sample Login Credentials ===");
        $this->command->info("Admin: admin@dshirts.com / admin123");
        $this->command->info("Staff: staff@dshirts.com / staff123");
        $this->command->info("Customer: customer@dshirts.com / customer123");
        $this->command->info("Additional Customers:");
        $this->command->info("  - ahmed@example.com / customer123");
        $this->command->info("  - sarah@example.com / customer123");
        $this->command->info("  - mohamed@example.com / customer123");
    }
}