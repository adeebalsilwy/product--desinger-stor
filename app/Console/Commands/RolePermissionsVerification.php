<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionsVerification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:role-permissions-verification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify role-permission assignments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== ROLE-PERMISSION ASSIGNMENT VERIFICATION ===');
        $this->newLine();

        // Get all roles with their permissions
        $roles = Role::with('permissions')->get();

        foreach ($roles as $role) {
            $this->line(strtoupper($role->name) . ' ROLE:');
            $this->line('Slug: ' . $role->slug);
            $this->line('Description: ' . $role->description);
            $this->line('JSON Permissions Count: ' . (is_array($role->permissions) ? count($role->permissions) : 0));
            $this->line('Status: ' . ($role->is_active ? 'Active' : 'Inactive'));
            $this->line(str_repeat('-', 40));
            
            // Show permissions from JSON array
            if (is_array($role->permissions) && count($role->permissions) > 0) {
                $this->line('Assigned Permissions (JSON Array): ');
                $permissionIds = $role->permissions;
                $permissions = Permission::whereIn('id', $permissionIds)->orderBy('resource')->orderBy('name')->get();
                foreach ($permissions as $permission) {
                    $this->line('  • ' . $permission->name . ' (' . $permission->slug . ')');
                }
            } else {
                $this->line('No permissions assigned in JSON array');
            }
            
            // Show permissions from role_permissions table
            $tablePermissions = DB::table('role_permissions')->where('role_id', $role->id)->count();
            $this->line('Role-Permission Table Relationships: ' . $tablePermissions);
            
            $this->newLine();
        }

        $this->info('=== SUMMARY ===');
        $this->line('Total Roles: ' . Role::count());
        $this->line('Total Permissions: ' . Permission::count());
        $this->line('Active Roles: ' . Role::where('is_active', true)->count());
        $this->line('Role-Permission Relationships: ' . \DB::table('role_permissions')->count());
        
        $this->newLine();
        $this->info('Verification completed successfully!');
    }
}