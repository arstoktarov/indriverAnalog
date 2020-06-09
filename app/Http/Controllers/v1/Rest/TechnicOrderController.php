<?php

namespace App\Http\Controllers\v1\Rest;

use App\Http\Controllers\Controller;
use App\Models\TechnicOrder;
use Illuminate\Http\Request;

class TechnicOrderController extends Controller
{
    public function index(Request $request) {
        $orders = $request['user']->technicOrders()->paginate(Controller::PAGINATE_COUNT);
        return $orders;
    }

    public function show($id, Request $request) {
        $order = TechnicOrder::find($id);
        if (!$order) return $this->Result(404, null, 'Order not found');

        return $order;
    }


}
