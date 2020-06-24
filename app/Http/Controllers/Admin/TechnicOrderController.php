<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnicOrder;
use Illuminate\Http\Request;

class TechnicOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = TechnicOrder::with('city', 'technic')->orderBy('id', 'desc')->paginate(20);

        return view('admin.order.technic', ['orders' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TechnicOrder  $technicOrder
     * @return \Illuminate\Http\Response
     */
    public function show(TechnicOrder $technicOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TechnicOrder  $technicOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(TechnicOrder $technicOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TechnicOrder  $technicOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TechnicOrder $technicOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TechnicOrder  $technicOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(TechnicOrder $technicOrder)
    {
        $technicOrder->delete();
        return back()->withMessage('Успешно удалено!');
    }
}
