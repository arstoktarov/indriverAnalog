<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $hidden = ['api_visible', 'created_at', 'updated_at'];
}
