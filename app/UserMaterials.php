<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMaterials extends Model
{
    protected $table = 'user_materials';
    protected $fillable = ['user_id', 'material_id'];
    protected $hidden = ['pivot'];
}
