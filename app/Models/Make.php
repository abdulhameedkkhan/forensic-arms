<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Make extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
    
    /**
     * Scope a query to only include makes with specific columns.
     */
    public function scopeWithMinimalColumns($query)
    {
        return $query->select(['id', 'name']);
    }
}