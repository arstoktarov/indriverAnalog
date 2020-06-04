<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class EditProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|min:3',
            'city_id' => 'numeric|exists:cities,id',
            'password' => 'string|min:3',
            'push' => 'boolean',
            'sound' => 'boolean',
            'lang' => 'in:en,ru',
            'type' => 'in:1,2'
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $data = [
            'statusCode' => 400,
            'message' => $validator->errors()->first(),
            'data' => null,
        ];
        throw new HttpResponseException(response()->json($data, $data['statusCode']));
    }
}
