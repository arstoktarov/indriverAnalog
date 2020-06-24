<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialOrderResponse extends Model
{
    protected $table = 'material_order_responses';
    protected $hidden = ['created_at', 'updated_at'];
    protected $fillable = ['user_id', 'order_id', 'price'];
    protected $casts = [
        'user_id' => 'integer',
        'order_id' => 'integer',
        'price' => 'integer'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function executor() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order() {
        return $this->belongsTo(MaterialOrder::class);
    }
}
