<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicCharacteristics extends Model
{
    protected $table = 't_characteristics';
    protected $hidden = ['created_at', 'updated_at'];

    public function type() {
        return $this->belongsTo(CharacteristicType::class);
    }
}
