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
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    /**
     * Get the arm dealer that owns the weapon.
     */
    public function armDealer(): BelongsTo
    {
        return $this->belongsTo(ArmDealer::class, 'arm_dealer_id');
    }
}
