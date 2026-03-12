<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GuestAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'guest-admin@guest-admin.com'],
            [
                'name' => 'Admin',
                'password' => '00000000',
                'role' => 'admin',
            ]
        );
    }
}
