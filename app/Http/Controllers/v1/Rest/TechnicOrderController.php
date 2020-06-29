<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\TechnicOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TechnicOrderController extends Controller
{
    public function index(Request $request) {
        $orders = $request['user']->technicOrders()->with([
            'city', 'user', 'executor',
            'technic' => function($query) {
                $query->with('type');
            }
        ])
        ->paginate(Controller::PAGINATE_COUNT);
        return $orders;
    }

    public function show($id, Request $request) {
        $order = TechnicOrder::find($id);
        if (!$order) return $this->Result(404, null, 'Order not found');

        return $order;
    }

    public function doneOrder(Request $request) {
        $rules = [
            'order_uuid' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) return $this->Result(400, null, $validator->errors()->first());

        $technicOrder = TechnicOrder::where('uuid', $request['order_uuid'])->first();

        if (!$technicOrder) return $this->Result(404);
        $technicOrder->status = TechnicOrder::STATUS_DONE;
        $technicOrder->save();
    }
}
