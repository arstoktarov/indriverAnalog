<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{

    protected static function boot()
    {
        parent::boot();
//        self::addGlobalScope('typeJoined', function($query) {
//            $query->join('m_types', 'm_types.id', 'materials.type_id')
//                ->addSelect('materials.*')->withTypeColumns();
//        });
    }

    protected $hidden = ['created_at', 'updated_at', 'type_id', 'material_id', 'user_id'];

    public function type() {
        return $this->belongsTo(MaterialType::class);
    }

    public function scopeWithTypeColumns($query) {
        return $query->addSelect('m_types.charac_title', 'm_types.charac_unit', 'm_types.title');
    }

}
