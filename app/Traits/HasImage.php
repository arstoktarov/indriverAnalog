<?php

namespace App\Traits;
use Illuminate\Support\Facades\Storage;

trait HasImage {

    public function setImageAttribute($file) {
        if ($this->getAttribute('image')) {
            Storage::disk('public')->delete($this->attributes['image']);
        }
        $filePath = Storage::disk('public')->put('uploads/'.($this->imageFolder ?? $this->table.'/images'), $file, 'public');
        $this->attributes['image'] = $filePath;
    }

    public static function bootHasImage() {
        static::deleting(function($model) {
            if ($model->getAttribute('image')) {
                Storage::disk('public')->delete($model->attributes['image']);
            }
        });
    }



}