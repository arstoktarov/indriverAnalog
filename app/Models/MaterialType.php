<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialType extends Model
{
    protected $table = 'm_types';
    protected $hidden = ['created_at', 'updated_at'];


    public function materials() {
        return $this->hasMany(Material::class, 'type_id');
    }
}
