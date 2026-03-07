<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // Store permissions as JSON
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
        });

        // Create permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('resource'); // e.g., 'users', 'products', 'orders'
            $table->string('action'); // e.g., 'view', 'create', 'update', 'delete'
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['resource', 'action']);
            $table->index('is_active');
        });

        // Create user_roles pivot table
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'role_id']);
        });

        // Create role_permissions pivot table
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });

        // Add role column to users table (for backward compatibility)
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('user')->after('password');
            });
        }

        // Add user profile fields if they don't exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'zip_code')) {
                $table->string('zip_code')->nullable()->after('country');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });

        // Seed default roles and permissions
        $this->seedDefaultRolesAndPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        
        // Remove added columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'address',
                'city',
                'country',
                'zip_code',
                'is_active'
            ]);
        });
    }

    /**
     * Seed default roles and permissions
     */
    private function seedDefaultRolesAndPermissions()
    {
        // Create default permissions
        $permissions = [
            // User permissions
            ['name' => 'View Users', 'slug' => 'users.view', 'resource' => 'users', 'action' => 'view', 'description' => 'Can view user list'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'resource' => 'users', 'action' => 'create', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'slug' => 'users.update', 'resource' => 'users', 'action' => 'update', 'description' => 'Can edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'resource' => 'users', 'action' => 'delete', 'description' => 'Can delete users'],

            // Product permissions
            ['name' => 'View Products', 'slug' => 'products.view', 'resource' => 'products', 'action' => 'view', 'description' => 'Can view product list'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'resource' => 'products', 'action' => 'create', 'description' => 'Can create new products'],
            ['name' => 'Edit Products', 'slug' => 'products.update', 'resource' => 'products', 'action' => 'update', 'description' => 'Can edit existing products'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'resource' => 'products', 'action' => 'delete', 'description' => 'Can delete products'],

            // Order permissions
            ['name' => 'View Orders', 'slug' => 'orders.view', 'resource' => 'orders', 'action' => 'view', 'description' => 'Can view order list'],
            ['name' => 'Update Orders', 'slug' => 'orders.update', 'resource' => 'orders', 'action' => 'update', 'description' => 'Can update order status'],
            ['name' => 'View Order Details', 'slug' => 'orders.details', 'resource' => 'orders', 'action' => 'details', 'description' => 'Can view detailed order information'],

            // Design permissions
            ['name' => 'View Designs', 'slug' => 'designs.view', 'resource' => 'designs', 'action' => 'view', 'description' => 'Can view design list'],
            ['name' => 'Edit Designs', 'slug' => 'designs.update', 'resource' => 'designs', 'action' => 'update', 'description' => 'Can edit designs'],
            ['name' => 'Delete Designs', 'slug' => 'designs.delete', 'resource' => 'designs', 'action' => 'delete', 'description' => 'Can delete designs'],

            // Report permissions
            ['name' => 'View Reports', 'slug' => 'reports.view', 'resource' => 'reports', 'action' => 'view', 'description' => 'Can view system reports'],

            // Admin permissions
            ['name' => 'Access Admin Panel', 'slug' => 'admin.access', 'resource' => 'admin', 'action' => 'access', 'description' => 'Can access admin panel'],
        ];

        foreach ($permissions as $permissionData) {
            \App\Models\Permission::create($permissionData);
        }

        // Create default roles
        $adminRole = \App\Models\Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Full system access',
            'permissions' => \App\Models\Permission::pluck('id')->toArray(), // All permissions
        ]);

        $staffRole = \App\Models\Role::create([
            'name' => 'Staff',
            'slug' => 'staff',
            'description' => 'Limited admin access',
            'permissions' => \App\Models\Permission::whereIn('slug', [
                'users.view', 'users.update',
                'products.view', 'products.create', 'products.update',
                'orders.view', 'orders.update', 'orders.details',
                'designs.view', 'designs.update',
                'reports.view',
                'admin.access',
            ])->pluck('id')->toArray(),
        ]);

        $customerRole = \App\Models\Role::create([
            'name' => 'Customer',
            'slug' => 'customer',
            'description' => 'Regular customer',
            'permissions' => \App\Models\Permission::whereIn('slug', [
                'admin.access',
            ])->pluck('id')->toArray(),
        ]);
    }
};
