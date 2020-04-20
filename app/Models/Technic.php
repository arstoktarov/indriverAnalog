<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Technic extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['image'];


    public function category() {
        return $this->belongsTo(TechnicCategory::class);
    }

    public function characteristics() {
        return $this->hasMany(TechnicCharacteristics::class);
    }

    public function getImageAttribute() {
        return asset($this->attributes['image']);
    }

}
