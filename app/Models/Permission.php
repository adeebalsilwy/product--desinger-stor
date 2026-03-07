<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'resource',
        'action',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForResource($query, $resource)
    {
        return $query->where('resource', $resource);
    }

    public function scopeForAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeForResourceAction($query, $resource, $action)
    {
        return $query->where('resource', $resource)
                    ->where('action', $action);
    }
}
