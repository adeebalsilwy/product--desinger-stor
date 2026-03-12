<?php

// This script analyzes and displays the complete permissions structure
require_once 'vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== COMPLETE PERMISSIONS ANALYSIS ===\n\n";

// Get all permissions grouped by resource
$permissions = \App\Models\Permission::orderBy('resource')->orderBy('name')->get();

echo "TOTAL PERMISSIONS: " . $permissions->count() . "\n\n";

$resources = $permissions->groupBy('resource');

foreach ($resources as $resource => $perms) {
    echo strtoupper($resource) . " PERMISSIONS (" . $perms->count() . "):\n";
    echo str_repeat("-", 50) . "\n";
    
    foreach ($perms as $permission) {
        echo "  • " . $permission->name . "\n";
        echo "    Slug: " . $permission->slug . "\n";
        echo "    Action: " . $permission->action . "\n";
        echo "    Description: " . $permission->description . "\n";
        echo "    Active: " . ($permission->is_active ? 'Yes' : 'No') . "\n";
        echo "    ---\n";
    }
    echo "\n";
}

echo "=== PERMISSIONS BY ACTION TYPE ===\n\n";

$actions = $permissions->groupBy('action');
foreach ($actions as $action => $perms) {
    echo ucfirst($action) . " actions (" . $perms->count() . "):\n";
    foreach ($perms->take(5) as $perm) {
        echo "  - " . $perm->name . " (" . $perm->resource . ")\n";
    }
    if ($perms->count() > 5) {
        echo "  ... and " . ($perms->count() - 5) . " more\n";
    }
    echo "\n";
}

echo "=== DATABASE INTEGRITY CHECK ===\n\n";
echo "Total permissions: " . \App\Models\Permission::count() . "\n";
echo "Unique slugs: " . \App\Models\Permission::distinct('slug')->count('slug') . "\n";
echo "Unique names: " . \App\Models\Permission::distinct('name')->count('name') . "\n";
echo "Active permissions: " . \App\Models\Permission::where('is_active', true)->count() . "\n";
echo "Inactive permissions: " . \App\Models\Permission::where('is_active', false)->count() . "\n";

echo "\n=== PERMISSIONS SUMMARY ===\n\n";
echo "Resources covered: " . $resources->count() . "\n";
echo "Most comprehensive resource: " . $resources->sortByDesc(function($perms) { return $perms->count(); })->keys()->first() . " (" . $resources->max(function($perms) { return $perms->count(); }) . " permissions)\n";
echo "Least comprehensive resource: " . $resources->sortBy(function($perms) { return $perms->count(); })->keys()->first() . " (" . $resources->min(function($perms) { return $perms->count(); }) . " permissions)\n";

echo "\nAnalysis completed successfully!\n";