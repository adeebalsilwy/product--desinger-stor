<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermissionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Permission::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('resource', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $permissions = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug',
            'resource' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        Permission::create($validated);

        return redirect()->route('admin.permissions.index')->with('message', 'Permission created successfully.');
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $permission->id,
            'resource' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $permission->update($validated);

        return redirect()->route('admin.permissions.index')->with('message', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        // Don't allow deleting permissions that are assigned to roles
        if ($permission->roles()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete permission assigned to roles.']);
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('message', 'Permission deleted successfully.');
    }

    public function toggleStatus(Request $request, Permission $permission)
    {
        $permission->update(['is_active' => !$permission->is_active]);

        return redirect()->back()->with('message', 'Permission status updated successfully.');
    }
}