<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PermissionsAnalysis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:permissions-analysis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze and display comprehensive permissions structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== COMPLETE PERMISSIONS ANALYSIS ===');
        $this->newLine();

        // Get all permissions grouped by resource
        $permissions = \App\Models\Permission::orderBy('resource')->orderBy('name')->get();

        $this->info('TOTAL PERMISSIONS: ' . $permissions->count());
        $this->newLine();

        $resources = $permissions->groupBy('resource');

        foreach ($resources as $resource => $perms) {
            $this->line(strtoupper($resource) . ' PERMISSIONS (' . $perms->count() . '):');
            $this->line(str_repeat('-', 50));
            
            foreach ($perms as $permission) {
                $this->line('  • ' . $permission->name);
                $this->line('    Slug: ' . $permission->slug);
                $this->line('    Action: ' . $permission->action);
                $this->line('    Description: ' . $permission->description);
                $this->line('    Active: ' . ($permission->is_active ? 'Yes' : 'No'));
                $this->line('    ---');
            }
            $this->newLine();
        }

        $this->info('=== DATABASE INTEGRITY CHECK ===');
        $this->newLine();
        $this->line('Total permissions: ' . \App\Models\Permission::count());
        $this->line('Unique slugs: ' . \App\Models\Permission::distinct('slug')->count('slug'));
        $this->line('Unique names: ' . \App\Models\Permission::distinct('name')->count('name'));
        $this->line('Active permissions: ' . \App\Models\Permission::where('is_active', true)->count());
        $this->line('Inactive permissions: ' . \App\Models\Permission::where('is_active', false)->count());

        $this->newLine();
        $this->info('Analysis completed successfully!');
    }
}
