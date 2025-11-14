<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Range extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the users for this range.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'range_id');
    }

    /**
     * Get the weapons for this range.
     */
    public function weapons(): HasMany
    {
        return $this->hasMany(Weapon::class, 'range_id');
    }
}
