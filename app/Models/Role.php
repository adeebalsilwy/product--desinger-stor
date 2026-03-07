<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function hasPermission($permissionSlug)
    {
        // Check if role has direct permission
        $directPermission = $this->permissions()
            ->where('permissions.slug', $permissionSlug)
            ->exists();

        if ($directPermission) {
            return true;
        }

        // Check if permission is in the JSON permissions array
        if ($this->permissions) {
            return in_array($permissionSlug, $this->permissions);
        }

        return false;
    }

    public function assignPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        return $this->permissions()->attach($permission);
    }

    public function revokePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->firstOrFail();
        }

        return $this->permissions()->detach($permission);
    }

    public function getPermissionNamesAttribute()
    {
        return $this->permissions()->pluck('name')->toArray();
    }
}
