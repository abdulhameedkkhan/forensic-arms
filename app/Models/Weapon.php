<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weapon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cnic',
        'weapon_no',
        'arm_dealer_id',
        'fsl_diary_no',
        'license_no',
        'weapon_type_id',
        'bore_id',
        'make_id',
        'license_issuer_id',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    /**
     * Set the CNIC attribute - clean and save as normal string
     */
    public function setCnicAttribute($value)
    {
        if (is_array($value)) {
            // Convert array to comma-separated string
            $value = implode(', ', array_filter($value, fn($v) => !empty($v)));
        } elseif (is_string($value)) {
            // Clean the string - remove extra quotes and whitespace
            $value = trim($value, '"\'');
        }
        
        $this->attributes['cnic'] = $value;
    }

    /**
     * Get the arm dealer that owns the weapon.
     */
    public function armDealer(): BelongsTo
    {
        return $this->belongsTo(ArmDealer::class, 'arm_dealer_id');
    }

    /**
     * Get the weapon type.
     */
    public function weaponType(): BelongsTo
    {
        return $this->belongsTo(WeaponType::class, 'weapon_type_id');
    }

    /**
     * Get the bore.
     */
    public function bore(): BelongsTo
    {
        return $this->belongsTo(Bore::class, 'bore_id');
    }

    /**
     * Get the make.
     */
    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class, 'make_id');
    }

    /**
     * Get the license issuer.
     */
    public function licenseIssuer(): BelongsTo
    {
        return $this->belongsTo(LicenseIssuer::class, 'license_issuer_id');
    }
}
