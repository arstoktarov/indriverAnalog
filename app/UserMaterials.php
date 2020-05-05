<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserMaterials extends Model
{
    protected $table = 'user_materials';
    protected $fillable = ['user_id', 'material_id', 'description', 'image'];
    protected $hidden = ['pivot'];

    public function setImageAttribute($file) {
        //Storage::disk('public')->delete($this->image);
        $this->image = Storage::disk('public')->put(User::MATERIAL_IMAGES_PATH, $file);
    }
}
