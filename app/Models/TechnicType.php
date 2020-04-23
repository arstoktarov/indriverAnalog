<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicType extends Model
{
    protected $table = 't_types';
    protected $hidden = ['created_at', 'updated_at'];

    public function technics() {
        return $this->hasMany(Technic::class, 'type_id');
    }
}
