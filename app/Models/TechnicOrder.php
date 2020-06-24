<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechnicOrder extends Model
{
    const STATUS_NOT_STARTED = 0;
    const STATUS_IN_PROCESS = 1;
    const STATUS_DONE = 2;
    const STATUS_CANCELED = 3;

    protected $table = 't_orders';
    protected $hidden = ['created_at', 'updated_at'];
    protected $appends = ['status_name'];


    public function getStatusNameAttribute() {
        return trans("order_statuses.technic.$this->status");
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function technic() {
        return $this->belongsTo(Technic::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function executor() {
        return $this->belongsTo(User::class);
    }
}
