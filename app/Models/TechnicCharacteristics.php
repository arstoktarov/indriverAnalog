<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicCharacteristics extends Model
{
    protected $table = 't_characteristics';

    public function type() {
        return $this->belongsTo(CharacteristicType::class);
    }
}
