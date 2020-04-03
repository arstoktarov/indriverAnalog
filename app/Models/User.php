<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'city_id', 'push', 'sound', 'lang',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'phone_verified_at', 'city_id', 'created_at', 'updated_at'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'city_id' => 'integer',
        'push' => 'integer',
        'balance' => 'integer',
        'sound' => 'integer',
        'type' => 'integer',
    ];


    public function city() {
        return $this->belongsTo(City::class);
    }


    public function setPassword($value) {
        $this->attributes['password'] = $value;
        $this->resetToken();
    }

    public function resetToken() {
        $this->attributes['token'] = Str::random(30);
    }

    public function getVerifiedAttribute() {
        return isset($this->attributes['phone_verified_at']);
    }

    public function verify() {
        $this->attributes['phone_verified_at'] = Carbon::now();
        $this->resetToken();
        $this->save();
    }

    public function scopeVerified($query) {
        return $query->whereNotNull('phone_verified_at');
    }

/*    public function setCode($value) {
        $code_last_sent = $this->attributes['code_last_sent'] ?? null;
        if ($code_last_sent < now()->subSeconds(User::$codeTTL)) {
            $this->attributes['code_last_sent'] = now();
            $this->attributes['code'] = $value;
            return true;
        }
        return false;
    }*/
}
