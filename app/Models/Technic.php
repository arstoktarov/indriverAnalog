<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Technic extends Model
{
    protected $hidden = ['created_at', 'updated_at', 'specification', 'pivot', 'type_id', 'user_id', 'technic_id'];
    //protected $appends = ['image'];


    public function type() {
        return $this->belongsTo(TechnicType::class);
    }

//    public function getImageAttribute() {
//        return asset('public/'.$this->attributes['image']);
//    }


    public function scopeWithType($query, $type_id) {
        if (isset($type_id)) {
            return $query->where('type_id', $type_id);
        }
        return $query;
    }

    //public function scopeSelect('')

}
