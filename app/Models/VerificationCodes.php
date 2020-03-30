<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationCodes extends Model
{
    public static $codeTTL = 30;

    protected $fillable = ['phone'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];


}
