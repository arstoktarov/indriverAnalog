<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Technic extends Model
{
    protected $hidden = ['created_at', 'updated_at'];


    public function category() {
        return $this->belongsTo(TechnicCategory::class);
    }

    public function characteristics() {
        return $this->hasMany(TechnicCharacteristics::class);
    }

}
