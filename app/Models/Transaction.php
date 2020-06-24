<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    const FILLABLES = [
        'pg_amount', 'pg_net_amount', 'pg_ps_amount',
        'pg_ps_full_amount', 'pg_ps_currency', 'pg_payment_system',
        'pg_description', 'pg_result', 'pg_payment_date',
        'pg_user_phone', 'pg_user_contact_email',
        'pg_testing_mode', 'pg_card_pan',
        'pg_card_owner', 'pg_card_brand'
    ];

    protected $fillable = self::FILLABLES;

    public function user() {
        return $this->belongsTo(User::class);
    }
}
