<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\MaterialOrder;
use App\Models\MaterialOrderResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialOrderController extends Controller
{

    public function index(Request $request) {
        $user = $request['user'];

        $materialOrders = $user->materialOrders()->paginate(Controller::PAGINATE_COUNT);
        return $materialOrders;
    }

    public function ordersList(Request $request) {
        $user = $request['user'];

        $material_ids = $user->materials->pluck('id');

        $orders = MaterialOrder::whereIn('material_id', $material_ids)
            ->orderByDesc('created_at')
            ->paginate(Controller::PAGINATE_COUNT);

        return $orders;
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

    public function createResponse(Request $request) {
        $rules = [
            'order_id' => 'required|numeric',
            'price' => 'numeric'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $m_order = MaterialOrder::find($request['order_id']);

        if (!$m_order) return $this->Result(404, null, 'Material not found');

        $response = MaterialOrderResponse::firstOrNew([
            'user_id' => $request['user']->id,
            'order_id' => $m_order->id,
            'price' => $request['price']
        ]);
        $response->save();

        return $response;
    }

    public function responses($id, Request $request) {
        $materialOrder = MaterialOrder::find($id);
        return $materialOrder->responses;
    }

    public function chooseExecutor(Request $request) {
        $rules = [
            'response_id' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = $request['user'];

        $response = MaterialOrderResponse::find($request['response_id']);

        if (!$response) return $this->Result(404, null, 'Response not found');

        $materialOrder = $user->materialOrders()->find($response->order_id);

        if ($response->price) $materialOrder->price = $response->price;
        $materialOrder->status = MaterialOrder::STATUS_IN_PROCESS;
        $materialOrder->save();

        return $materialOrder;
    }

}
