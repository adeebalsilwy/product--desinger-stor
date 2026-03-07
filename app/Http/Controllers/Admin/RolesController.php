<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
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

        $roles = $query->withCount('users')->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
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
}