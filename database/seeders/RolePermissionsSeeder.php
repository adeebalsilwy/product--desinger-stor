<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== ASSIGNING PERMISSIONS TO ROLES ===');
        
        // Get all permissions
        $allPermissions = Permission::pluck('id')->toArray();
        
        // Administrator Role - Full access
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync($allPermissions);
            $this->command->info("✓ Administrator role assigned all " . count($allPermissions) . " permissions");
        }
        
        // Staff Role - Limited admin access
        $staffRole = Role::where('slug', 'staff')->first();
        if ($staffRole) {
            $staffPermissions = Permission::whereIn('slug', [
                // User management (limited)
                'users.view', 'users.update', 'users.view-permissions',
                // Product management
                'products.view', 'products.create', 'products.update', 'products.manage-variants', 'products.view-analytics',
                // Order management
                'orders.view', 'orders.update', 'orders.details', 'orders.manage-status', 'orders.view-reports',
                // Design management
                'designs.view', 'designs.update', 'designs.approve', 'designs.view-analytics',
                // Template management
                'templates.view', 'templates.create', 'templates.update', 'templates.approve',
                // Customer management
                'customers.view', 'customers.update', 'customers.view-orders',
                // Reports
                'reports.view', 'reports.view-revenue', 'analytics.view',
                // Admin access
                'admin.access', 'admin.dashboard',
                // Settings (view only)
                'settings.view',
            ])->pluck('id')->toArray();
            
            $staffRole->permissions()->sync($staffPermissions);
            $this->command->info("✓ Staff role assigned " . count($staffPermissions) . " permissions");
        }
        
        // Customer Role - Basic access
        $customerRole = Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $customerPermissions = Permission::whereIn('slug', [
                'admin.access', // Basic admin panel access
                'products.view', // View products
                'designs.view', // View designs
                'templates.view', // View templates
                'orders.view', // View own orders
                'orders.details', // View own order details
                'customers.view-orders', // View order history
            ])->pluck('id')->toArray();
            
            $customerRole->permissions()->sync($customerPermissions);
            $this->command->info("✓ Customer role assigned " . count($customerPermissions) . " permissions");
        }
        
        // Designer Role - Specialized access
        $designerRole = Role::where('slug', 'designer')->first();
        if ($designerRole) {
            $designerPermissions = Permission::whereIn('slug', [
                // Design management (full)
                'designs.view', 'designs.create', 'designs.update', 'designs.delete', 'designs.approve', 'designs.manage-templates', 'designs.view-analytics',
                // Template management (full)
                'templates.view', 'templates.create', 'templates.update', 'templates.delete', 'templates.approve',
                // Clipart management
                'cliparts.view', 'cliparts.upload', 'cliparts.update', 'cliparts.delete', 'cliparts.manage-categories',
                // Font management
                'fonts.view', 'fonts.upload', 'fonts.delete',
                // Asset management
                'assets.view', 'assets.upload', 'assets.delete', 'assets.manage-categories',
                // Print areas
                'print-areas.view', 'print-areas.manage',
                // Admin access
                'admin.access', 'admin.dashboard',
                // Reports
                'reports.view', 'analytics.view',
            ])->pluck('id')->toArray();
            
            $designerRole->permissions()->sync($designerPermissions);
            $this->command->info("✓ Designer role assigned " . count($designerPermissions) . " permissions");
        }
        
        // Role-permission relationships are managed by Eloquent
        
        $this->command->info('=== ROLE-PERMISSION ASSIGNMENT COMPLETED ===');
        $this->command->info('Total role-permission relationships: ' . DB::table('role_permissions')->count());
    }
    

}