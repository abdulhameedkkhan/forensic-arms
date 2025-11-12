<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bore extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
