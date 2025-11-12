<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArmDealer extends Model
{
    use SoftDeletes;

    protected $table = 'armorers';

    protected $fillable = [
        'name',
        'address',
        'cell',
        'phone',
        'email',
        'longitude',
        'latitude',
        'shop_name',
        'license_number',
        'license_expiry',
        'city',
        'district',
        'police_station',
        'postal_code',
        'status',
        'notes',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'longitude' => 'decimal:8',
        'latitude' => 'decimal:8',
    ];
}
