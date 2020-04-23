<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $hidden = ['created_at', 'updated_at', 'type_id'];

    public function type(){
        return $this->belongsTo(MaterialType::class);
    }
}
