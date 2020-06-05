<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicOrder extends Model
{
    protected $table = 't_orders';
    protected $hidden = ['created_at', 'updated_at'];
}
