<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicenseIssuer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
    
    /**
     * Scope a query to only include license issuers with specific columns.
     */
    public function scopeWithMinimalColumns($query)
    {
        return $query->select(['id', 'name']);
    }
}