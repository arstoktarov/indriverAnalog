<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicCharacteristics extends Model
{

    protected $table = 't_characteristics';
    protected $hidden = ['created_at', 'updated_at', 'type_id', 'technic_id'];

    public function type() {
        return $this->belongsTo(CharacteristicType::class);
    }

    public function scopeWithTypeString($query) {
        $query->join('characteristic_types', 'characteristic_types.id', 't_characteristics.type_id')
        ->select('t_characteristics.*', 'characteristic_types.title');
    }


}
