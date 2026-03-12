<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Order;
use App\Models\SavedDesign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['roles', 'orders', 'savedDesigns']);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $request->search,
                'role' => $request->role,
                'status' => $request->status,
            ],
            'roles' => Role::all(),
            'orders_count' => Order::count(),
            'customers_count' => \App\Models\Customer::count(),
            'tshirts_count' => \App\Models\Product::count(),
            'revenue' => Order::sum('total_amount'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $user = User::create($validated);

        return redirect()->route('admin.users.index')->with('message', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load('roles'),
            'roles' => Role::all(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('message', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        try {
            if ($user->id === auth()->id()) {
                return redirect()->back()->withErrors(['error' => 'You cannot delete your own account.']);
            }

            $user->delete();

            return redirect()->route('admin.users.index')->with('message', 'User deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete user ' . $user->id . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to delete user.']);
        }
    }

    public function show(User $user)
    {
        try {
            $user->loadMissing(['orders', 'savedDesigns', 'roles']);
            
            // Safely load permissions with fallback
            try {
                $user->loadMissing('permissions');
            } catch (\Exception $e) {
                \Log::warning('Failed to load permissions for user ' . $user->id . ': ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Failed to load user data ' . $user->id . ': ' . $e->getMessage());
        }

        // Safely load orders
        $orders = [];
        $designs = [];
        $orders_count = 0;
        $designs_count = 0;
        
        try {
            $orders = $user->orders()->with('items')->latest()->paginate(10);
            $orders_count = $user->orders()->count();
        } catch (\Exception $e) {
            \Log::warning('Failed to load orders for user ' . $user->id . ': ' . $e->getMessage());
        }
        
        try {
            $designs = $user->savedDesigns()->with(['user', 'productType'])->latest()->paginate(10);
            $designs_count = $user->savedDesigns()->count();
        } catch (\Exception $e) {
            \Log::warning('Failed to load designs for user ' . $user->id . ': ' . $e->getMessage());
        }

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'orders' => $orders,
            'designs' => $designs,
            'authUser' => auth()->user(),
            'orders_count' => $orders_count,
            'designs_count' => $designs_count,
        ]);
    }

    public function toggleStatus(User $user)
    {
        try {
            $user->update(['is_active' => !$user->is_active]);
            return redirect()->back()->with('message', 'User status updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to toggle user status ' . $user->id . ': ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to update user status.']);
        }
    }

    public function bulkAssignRole(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();
        $role = Role::find($request->role_id);

        foreach ($users as $user) {
            // Remove all existing roles
            $user->roles()->detach();
            
            // Assign the new role
            $user->roles()->attach($role->id);
            
            // Also update the legacy role field for backward compatibility
            $user->update(['role' => $role->slug]);
        }

        return redirect()->back()->with('message', count($users) . ' users updated successfully.');
    }

    public function bulkRemoveRole(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        foreach ($users as $user) {
            // Remove all roles
            $user->roles()->detach();
            
            // Set role to customer for backward compatibility
            $user->update(['role' => 'customer']);
        }

        return redirect()->back()->with('message', count($users) . ' users updated successfully.');
    }
}