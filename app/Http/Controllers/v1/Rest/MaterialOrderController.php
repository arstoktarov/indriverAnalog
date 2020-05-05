<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\MaterialOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialOrderController extends Controller
{

    public function index(Request $request) {
        $user = $request['user'];

        $materialOrders = MaterialOrder::where('user_id', $user->id)->get();
        return $materialOrders;
    }

    public function create(Request $request) {
        $rules = [
            'city_id' => 'required|exists:cities,id',
            'material_id' => 'required|exists:materials,id',
            'count' => 'required|numeric',
            'delivery_deadline' => 'required|date',
            'address' => 'string',
            'lat' => 'required',
            'long' => 'required',
            'price' => 'required|numeric',
            'description' => 'string'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors());

        $user = $request['user'];

        $materialOrder = MaterialOrder::create([
            'user_id' => $user->id,
            'city_id' => $request['city_id'],
            'material_id' => $request['material_id'],
            'count' => $request['count'],
            'delivery_deadline' => Carbon::make($request['delivery_deadline']),
            'address' => $request['address'],
            'lat' => $request['lat'],
            'long' => $request['long'],
            'price' =>$request['price'],
            'description' => $request['description'],
        ]);

        return response()->json($materialOrder);
    }
}
