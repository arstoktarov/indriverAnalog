<?php

namespace App\Http\Controllers\v1\Rest;

use App\Events\Push\MaterialOrders\ExecutorAccepted;
use App\Events\Push\MaterialOrders\ExecutorResponded;
use App\Http\Controllers\Controller;
use App\Models\MaterialOrder;
use App\Models\MaterialOrderResponse;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaterialOrderController extends Controller
{

    public function index(Request $request) {
        $user = $request['user'];

        $materialOrders = $user->materialOrders()->with([
            'user',
            'city',
            'material' => function($query) {
                $query->with('type');
            },
        ])->paginate(Controller::PAGINATE_COUNT);
        return $materialOrders;
    }

    public function ordersList(Request $request) {
        $user = $request['user'];

        $material_ids = $user->materials->pluck('id');

        $orders = MaterialOrder::whereIn('material_id', $material_ids)
            ->with([
                'user',
                'city',
                'material' => function($query) {
                    $query->with('type');
                },
            ])
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

        $materialOrder->refresh();

        $materialOrder->load([
            'user',
            'city',
            'material' => function($query) {
                $query->with('type');
            },
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
        $executor = $response->user;
        $order = $response->order;
        $user = $order->user;
        $response->save();
        $response->refresh()->load('user');

        event(new ExecutorResponded($user, $executor));

        return response()->json($response);
    }

    public function startOrder(Request $request) {
        $rules = [
            'order_id' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $m_order = MaterialOrder::find($request['order_id']);
        if (!$m_order) return $this->Result(404, null, 'Material not found');

        if ($m_order->executor_id) return $this->Result(400, null, 'Order already has executor');

        $m_order->executor_id = $request['user']->id;
        $m_order->status = MaterialOrder::STATUS_IN_PROCESS;
        $m_order->save();

        $m_order->refresh();

        $m_order->load([
            'user',
            'city',
            'material' => function($query) {
                $query->with('type');
            },
        ]);

        event(new ExecutorAccepted($m_order->user, $m_order->executor));

        return response()->json($m_order);
    }

    public function responses($id, Request $request) {
        $materialOrder = MaterialOrder::find($id);
        return $materialOrder->responses()->with(['user', 'order' => function($query) {
            $query->with(['executorMaterial', 'material', 'material.type']);
        }])->get();
    }

    public function chooseExecutor(Request $request) {
        $rules = [
            'response_id' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = $request['user'];

        $response = MaterialOrderResponse::find($request['response_id']);
        if (!$response) return $this->Result(404, null, 'Response not found'); //TODO add localized answer
        $executor = $response->executor;
        if (!$executor) return $this->Result(404, null, 'Executor not found'); //TODO add localized answer
        $materialOrder = $user->materialOrders()->find($response->order_id);
        $executor_material = $executor->materials()->find($materialOrder->material_id);
        if (!$executor) return $this->Result(404, null, 'Executor not found');
        if (!$executor_material) return $this->Result(404, null, 'Executor has no material for order');

        if ($response->price) $materialOrder->price = $response->price;
        $materialOrder->executor_id = $executor->id;
        $materialOrder->executor_material_id = $executor_material->id;
        $materialOrder->status = MaterialOrder::STATUS_IN_PROCESS;
        $materialOrder->save();

        event($request['user'], $materialOrder->executor);

        return $materialOrder;
    }

    public function declineExecutor(Request $request) {
        $rules = [
            'response_id' => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $user = $request['user'];

        $response = MaterialOrderResponse::find($request['response_id']);
        if (!$response) return $this->Result(404, null, 'Response not found');

        $response->delete();

        $materialOrder = $user->materialOrders()->find($response->order_id);

        return $materialOrder->responses;
    }

}
