<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharacteristicType extends Model
{
    protected $table = 'characteristic_types';
    protected $hidden = ['created_at', 'updated_at'];
}
