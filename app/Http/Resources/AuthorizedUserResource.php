<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorizedUserResource extends JsonResource
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
            'type' => $model->type,
            'name' => $model->name,
            'phone' => $model->phone,
            'city_id' => $model->city_id,
            'balance' => $model->balance,
            'push' => $model->push,
            'sound' => $model->sound,
            'lang' => $model->lang,
            'token' => $model->token,
            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at,
            'city' => $model->city,
        ];
    }
}
