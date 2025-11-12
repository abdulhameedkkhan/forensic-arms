<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'slug',
        'description',
    ];

    /**
     * Check if role has a specific permission by name.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->hasPermissionTo($permission);
    }
}

