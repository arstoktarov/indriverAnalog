<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserTechnicResource extends JsonResource
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
            'technic_id' => $model->pivot->technic_id,
            'image' => $model->pivot->image,
            'description' => $model->pivot->description,
            'model' => $model->pivot->model,
            'type' => $model->type
        ];
    }
}
