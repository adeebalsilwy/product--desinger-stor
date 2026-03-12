<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'city',
        'country',
        'zip_code',
        'is_active',
        'zipcode', // For customer compatibility
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'is_active' => 'boolean',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
    
    // Alias for customer relationship
    public function customerOrders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function savedDesigns()
    {
        return $this->hasMany(SavedDesign::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // Get permissions through assigned roles
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
                    ->withTimestamps();
    }
    
    // Alternative method to get all permissions through roles
    public function getAllPermissionsAttribute()
    {
        $permissions = collect();
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }
        return $permissions->unique('id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function hasRole($roleName)
    {
        if (is_string($roleName)) {
            return $this->roles->contains('name', $roleName) || $this->role === $roleName;
        }
        
        return $this->roles->contains('id', $roleName->id);
    }

    public function hasPermission($permissionSlug)
    {
        // Check direct permissions
        if ($this->permissions()->where('slug', $permissionSlug)->exists()) {
            return true;
        }
        
        // Check permissions through roles
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionSlug)) {
                return true;
            }
        }
        
        // Check traditional role field
        if ($this->role === 'admin') {
            return true; // Admin has all permissions
        }
        
        return false;
    }

    public function isAdmin()
    {
        return $this->role === 'admin' || $this->hasRole('admin');
    }

    public function isStaff()
    {
        return in_array($this->role, ['admin', 'staff']) || $this->hasRole('admin') || $this->hasRole('staff');
    }

    public function isCustomer()
    {
        return $this->role === 'customer' || $this->hasRole('customer');
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    public function getTotalOrdersAttribute()
    {
        return $this->orders()->count();
    }

    public function getTotalSpentAttribute()
    {
        return $this->orders()->where('payment_status', 'paid')->sum('total_amount');
    }
}
