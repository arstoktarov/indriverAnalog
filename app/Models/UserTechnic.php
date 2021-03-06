<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;

class UserTechnic extends Model
{
    use HasImage;

    protected $imageFolder = User::TECHNIC_IMAGES_PATH;

    protected $table = 'users_technics';

    protected $fillable = [
        'technic_id', 'user_id', 'image', 'description', 'model',
    ];
    protected $hidden = [
        'created_at', 'updated_at'
    ];
    protected $casts = [
        'technic_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function technic() {
        return $this->belongsTo(Technic::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
