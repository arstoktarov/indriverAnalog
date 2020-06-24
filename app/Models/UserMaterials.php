<?php

namespace App\Models;

use App\Models\User;
use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserMaterials extends Model
{
    use HasImage;

    protected $imageFolder = User::MATERIAL_IMAGES_PATH;

    protected $table = 'user_materials';
    protected $fillable = ['user_id', 'material_id', 'description', 'image'];
    protected $hidden = ['pivot', 'created_at', 'updated_at'];

    public function material() {
        return $this->belongsTo(Material::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

}
