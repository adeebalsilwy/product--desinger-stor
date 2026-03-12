<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $roles = $query->withCount('users')->with('permissions')->orderBy('created_at', 'desc')->paginate(20);
        $permissions = Permission::active()->orderBy('resource')->orderBy('name')->get();

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        Role::create($validated);

        return redirect()->route('admin.roles.index')->with('message', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $role->update($validated);

        return redirect()->route('admin.roles.index')->with('message', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Don't allow deleting roles that have users assigned
        if ($role->users()->count() > 0) {
            return redirect()->back()->withErrors(['error' => 'Cannot delete role with assigned users.']);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('message', 'Role deleted successfully.');
    }

    public function toggleStatus(Request $request, Role $role)
    {
        $role->update(['is_active' => !$role->is_active]);

        return redirect()->back()->with('message', 'Role status updated successfully.');
    }

    public function grantPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->syncWithoutDetaching($validated['permission_ids']);

        return redirect()->back()->with('message', 'Permissions granted successfully.');
    }

    public function revokePermission(Request $request, Role $role, Permission $permission)
    {
        $role->permissions()->detach($permission->id);

        return redirect()->back()->with('message', 'Permission revoked successfully.');
    }

    public function revokeAllPermissions(Request $request, Role $role)
    {
        $role->permissions()->detach();

        return redirect()->back()->with('message', 'All permissions revoked successfully.');
    }
}