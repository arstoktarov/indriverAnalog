<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $model = $this->resource;
        return [
            'id' => $model->id,
            'charac_value' => $model->charac_value,
            'material_id' => $model->pivot->material_id,
            'image' => $model->pivot->image,
            'description' => $model->pivot->description,
            'type' => $model->type
        ];
    }
}
