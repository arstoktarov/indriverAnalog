<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialOrder;
use Illuminate\Http\Request;

class MaterialOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = MaterialOrder::with('city', 'material')->orderBy('id', 'desc')->paginate(20);

        return view('admin.order.material', ['orders' => $orders]);
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
     * @param  \App\Models\MaterialOrder  $materialOrder
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialOrder $materialOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MaterialOrder  $materialOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(MaterialOrder $materialOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MaterialOrder  $materialOrder
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialOrder $materialOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MaterialOrder  $materialOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialOrder $materialOrder)
    {
        $materialOrder->delete();
        return back()->withMessage('Успешно удалено!');
    }
}
