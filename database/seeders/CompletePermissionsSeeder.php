<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class CompletePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all comprehensive permissions
        $permissions = [
            // User Management Permissions
            [
                'name' => 'View Users',
                'slug' => 'users.view',
                'description' => 'Can view user list and details',
                'resource' => 'users',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Create Users',
                'slug' => 'users.create',
                'description' => 'Can create new users',
                'resource' => 'users',
                'action' => 'create',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'users.update',
                'description' => 'Can edit existing users',
                'resource' => 'users',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'users.delete',
                'description' => 'Can delete users',
                'resource' => 'users',
                'action' => 'delete',
                'is_active' => true,
            ],
            [
                'name' => 'Manage User Roles',
                'slug' => 'users.manage-roles',
                'description' => 'Can assign and remove user roles',
                'resource' => 'users',
                'action' => 'manage-roles',
                'is_active' => true,
            ],
            [
                'name' => 'View User Permissions',
                'slug' => 'users.view-permissions',
                'description' => 'Can view user permissions',
                'resource' => 'users',
                'action' => 'view-permissions',
                'is_active' => true,
            ],

            // Product Management Permissions
            [
                'name' => 'View Products',
                'slug' => 'products.view',
                'description' => 'Can view product list and details',
                'resource' => 'products',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Create Products',
                'slug' => 'products.create',
                'description' => 'Can create new products',
                'resource' => 'products',
                'action' => 'create',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Products',
                'slug' => 'products.update',
                'description' => 'Can edit existing products',
                'resource' => 'products',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Products',
                'slug' => 'products.delete',
                'description' => 'Can delete products',
                'resource' => 'products',
                'action' => 'delete',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Product Variants',
                'slug' => 'products.manage-variants',
                'description' => 'Can manage product variants and options',
                'resource' => 'products',
                'action' => 'manage-variants',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Product Inventory',
                'slug' => 'products.manage-inventory',
                'description' => 'Can manage product inventory and stock levels',
                'resource' => 'products',
                'action' => 'manage-inventory',
                'is_active' => true,
            ],
            [
                'name' => 'View Product Analytics',
                'slug' => 'products.view-analytics',
                'description' => 'Can view product sales and performance analytics',
                'resource' => 'products',
                'action' => 'view-analytics',
                'is_active' => true,
            ],

            // Order Management Permissions
            [
                'name' => 'View Orders',
                'slug' => 'orders.view',
                'description' => 'Can view order list',
                'resource' => 'orders',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Update Orders',
                'slug' => 'orders.update',
                'description' => 'Can update order status',
                'resource' => 'orders',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'View Order Details',
                'slug' => 'orders.details',
                'description' => 'Can view detailed order information',
                'resource' => 'orders',
                'action' => 'details',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Order Status',
                'slug' => 'orders.manage-status',
                'description' => 'Can change order status and workflow',
                'resource' => 'orders',
                'action' => 'manage-status',
                'is_active' => true,
            ],
            [
                'name' => 'Process Order Payments',
                'slug' => 'orders.process-payments',
                'description' => 'Can process order payments and refunds',
                'resource' => 'orders',
                'action' => 'process-payments',
                'is_active' => true,
            ],
            [
                'name' => 'View Order Reports',
                'slug' => 'orders.view-reports',
                'description' => 'Can view order reports and analytics',
                'resource' => 'orders',
                'action' => 'view-reports',
                'is_active' => true,
            ],
            [
                'name' => 'Cancel Orders',
                'slug' => 'orders.cancel',
                'description' => 'Can cancel orders',
                'resource' => 'orders',
                'action' => 'cancel',
                'is_active' => true,
            ],

            // Design Management Permissions
            [
                'name' => 'View Designs',
                'slug' => 'designs.view',
                'description' => 'Can view design list',
                'resource' => 'designs',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Designs',
                'slug' => 'designs.update',
                'description' => 'Can edit designs',
                'resource' => 'designs',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Designs',
                'slug' => 'designs.delete',
                'description' => 'Can delete designs',
                'resource' => 'designs',
                'action' => 'delete',
                'is_active' => true,
            ],
            [
                'name' => 'Approve Designs',
                'slug' => 'designs.approve',
                'description' => 'Can approve customer designs',
                'resource' => 'designs',
                'action' => 'approve',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Design Templates',
                'slug' => 'designs.manage-templates',
                'description' => 'Can create and manage design templates',
                'resource' => 'designs',
                'action' => 'manage-templates',
                'is_active' => true,
            ],
            [
                'name' => 'View Design Analytics',
                'slug' => 'designs.view-analytics',
                'description' => 'Can view design usage analytics',
                'resource' => 'designs',
                'action' => 'view-analytics',
                'is_active' => true,
            ],

            // Role & Permission Management Permissions
            [
                'name' => 'View Roles',
                'slug' => 'roles.view',
                'description' => 'Can view role list and details',
                'resource' => 'roles',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Create Roles',
                'slug' => 'roles.create',
                'description' => 'Can create new roles',
                'resource' => 'roles',
                'action' => 'create',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Roles',
                'slug' => 'roles.update',
                'description' => 'Can edit existing roles',
                'resource' => 'roles',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Roles',
                'slug' => 'roles.delete',
                'description' => 'Can delete roles',
                'resource' => 'roles',
                'action' => 'delete',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Role Permissions',
                'slug' => 'roles.manage-permissions',
                'description' => 'Can assign and manage role permissions',
                'resource' => 'roles',
                'action' => 'manage-permissions',
                'is_active' => true,
            ],
            [
                'name' => 'View Permissions',
                'slug' => 'permissions.view',
                'description' => 'Can view permission list',
                'resource' => 'permissions',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Create Permissions',
                'slug' => 'permissions.create',
                'description' => 'Can create new permissions',
                'resource' => 'permissions',
                'action' => 'create',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Permissions',
                'slug' => 'permissions.update',
                'description' => 'Can edit existing permissions',
                'resource' => 'permissions',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Permissions',
                'slug' => 'permissions.delete',
                'description' => 'Can delete permissions',
                'resource' => 'permissions',
                'action' => 'delete',
                'is_active' => true,
            ],

            // Template Management Permissions
            [
                'name' => 'View Templates',
                'slug' => 'templates.view',
                'description' => 'Can view template list',
                'resource' => 'templates',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Create Templates',
                'slug' => 'templates.create',
                'description' => 'Can create new templates',
                'resource' => 'templates',
                'action' => 'create',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Templates',
                'slug' => 'templates.update',
                'description' => 'Can edit existing templates',
                'resource' => 'templates',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Templates',
                'slug' => 'templates.delete',
                'description' => 'Can delete templates',
                'resource' => 'templates',
                'action' => 'delete',
                'is_active' => true,
            ],
            [
                'name' => 'Approve Templates',
                'slug' => 'templates.approve',
                'description' => 'Can approve template submissions',
                'resource' => 'templates',
                'action' => 'approve',
                'is_active' => true,
            ],

            // Settings Management Permissions
            [
                'name' => 'View Settings',
                'slug' => 'settings.view',
                'description' => 'Can view system settings',
                'resource' => 'settings',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Update Settings',
                'slug' => 'settings.update',
                'description' => 'Can update system settings',
                'resource' => 'settings',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Brand Settings',
                'slug' => 'settings.manage-brand',
                'description' => 'Can manage brand and appearance settings',
                'resource' => 'settings',
                'action' => 'manage-brand',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Payment Settings',
                'slug' => 'settings.manage-payments',
                'description' => 'Can manage payment gateway settings',
                'resource' => 'settings',
                'action' => 'manage-payments',
                'is_active' => true,
            ],

            // Customer Management Permissions
            [
                'name' => 'View Customers',
                'slug' => 'customers.view',
                'description' => 'Can view customer list and details',
                'resource' => 'customers',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Customers',
                'slug' => 'customers.update',
                'description' => 'Can edit customer information',
                'resource' => 'customers',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Customers',
                'slug' => 'customers.delete',
                'description' => 'Can delete customers',
                'resource' => 'customers',
                'action' => 'delete',
                'is_active' => true,
            ],
            [
                'name' => 'View Customer Orders',
                'slug' => 'customers.view-orders',
                'description' => 'Can view customer order history',
                'resource' => 'customers',
                'action' => 'view-orders',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Customer Accounts',
                'slug' => 'customers.manage-accounts',
                'description' => 'Can manage customer accounts and status',
                'resource' => 'customers',
                'action' => 'manage-accounts',
                'is_active' => true,
            ],

            // Revenue & Reports Permissions
            [
                'name' => 'View Reports',
                'slug' => 'reports.view',
                'description' => 'Can view system reports',
                'resource' => 'reports',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'View Revenue Reports',
                'slug' => 'reports.view-revenue',
                'description' => 'Can view revenue and financial reports',
                'resource' => 'reports',
                'action' => 'view-revenue',
                'is_active' => true,
            ],
            [
                'name' => 'Export Reports',
                'slug' => 'reports.export',
                'description' => 'Can export reports in various formats',
                'resource' => 'reports',
                'action' => 'export',
                'is_active' => true,
            ],
            [
                'name' => 'View Analytics',
                'slug' => 'analytics.view',
                'description' => 'Can view system analytics and metrics',
                'resource' => 'analytics',
                'action' => 'view',
                'is_active' => true,
            ],

            // Clipart & Media Management Permissions
            [
                'name' => 'View Cliparts',
                'slug' => 'cliparts.view',
                'description' => 'Can view clipart library',
                'resource' => 'cliparts',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Upload Cliparts',
                'slug' => 'cliparts.upload',
                'description' => 'Can upload new cliparts',
                'resource' => 'cliparts',
                'action' => 'upload',
                'is_active' => true,
            ],
            [
                'name' => 'Edit Cliparts',
                'slug' => 'cliparts.update',
                'description' => 'Can edit clipart information',
                'resource' => 'cliparts',
                'action' => 'update',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Cliparts',
                'slug' => 'cliparts.delete',
                'description' => 'Can delete cliparts',
                'resource' => 'cliparts',
                'action' => 'delete',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Clipart Categories',
                'slug' => 'cliparts.manage-categories',
                'description' => 'Can manage clipart categories',
                'resource' => 'cliparts',
                'action' => 'manage-categories',
                'is_active' => true,
            ],
            [
                'name' => 'View Fonts',
                'slug' => 'fonts.view',
                'description' => 'Can view font library',
                'resource' => 'fonts',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Upload Fonts',
                'slug' => 'fonts.upload',
                'description' => 'Can upload new fonts',
                'resource' => 'fonts',
                'action' => 'upload',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Fonts',
                'slug' => 'fonts.delete',
                'description' => 'Can delete fonts',
                'resource' => 'fonts',
                'action' => 'delete',
                'is_active' => true,
            ],

            // Print Area Management Permissions
            [
                'name' => 'View Print Areas',
                'slug' => 'print-areas.view',
                'description' => 'Can view print areas configuration',
                'resource' => 'print-areas',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Print Areas',
                'slug' => 'print-areas.manage',
                'description' => 'Can manage print areas and configurations',
                'resource' => 'print-areas',
                'action' => 'manage',
                'is_active' => true,
            ],

            // Asset Management Permissions
            [
                'name' => 'View Assets',
                'slug' => 'assets.view',
                'description' => 'Can view asset library',
                'resource' => 'assets',
                'action' => 'view',
                'is_active' => true,
            ],
            [
                'name' => 'Upload Assets',
                'slug' => 'assets.upload',
                'description' => 'Can upload new assets',
                'resource' => 'assets',
                'action' => 'upload',
                'is_active' => true,
            ],
            [
                'name' => 'Delete Assets',
                'slug' => 'assets.delete',
                'description' => 'Can delete assets',
                'resource' => 'assets',
                'action' => 'delete',
                'is_active' => true,
            ],
            [
                'name' => 'Manage Asset Categories',
                'slug' => 'assets.manage-categories',
                'description' => 'Can manage asset categories',
                'resource' => 'assets',
                'action' => 'manage-categories',
                'is_active' => true,
            ],

            // Admin Panel Access Permissions
            [
                'name' => 'Access Admin Panel',
                'slug' => 'admin.access',
                'description' => 'Can access admin panel',
                'resource' => 'admin',
                'action' => 'access',
                'is_active' => true,
            ],
            [
                'name' => 'View Admin Dashboard',
                'slug' => 'admin.dashboard',
                'description' => 'Can view admin dashboard',
                'resource' => 'admin',
                'action' => 'dashboard',
                'is_active' => true,
            ],
            [
                'name' => 'Manage System Configuration',
                'slug' => 'admin.manage-system',
                'description' => 'Can manage system-level configuration',
                'resource' => 'admin',
                'action' => 'manage-system',
                'is_active' => true,
            ],

            // API Access Permissions
            [
                'name' => 'Access API',
                'slug' => 'api.access',
                'description' => 'Can access API endpoints',
                'resource' => 'api',
                'action' => 'access',
                'is_active' => true,
            ],
            [
                'name' => 'Manage API Keys',
                'slug' => 'api.manage-keys',
                'description' => 'Can manage API keys and tokens',
                'resource' => 'api',
                'action' => 'manage-keys',
                'is_active' => true,
            ],
        ];

        // Insert permissions without duplicates
        foreach ($permissions as $permissionData) {
            // Check if permission already exists
            $existing = Permission::where('slug', $permissionData['slug'])->first();
            
            if (!$existing) {
                Permission::create($permissionData);
                $this->command->info("Created permission: {$permissionData['name']} ({$permissionData['slug']})");
            } else {
                $this->command->warn("Permission already exists: {$permissionData['name']} ({$permissionData['slug']})");
            }
        }

        $this->command->info('Complete permissions seeder finished. Total permissions: ' . Permission::count());
    }
}