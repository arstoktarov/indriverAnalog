<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicCategory extends Model
{
    protected $table = 't_categories';

    protected $hidden = ['created_at', 'updated_at'];
}
