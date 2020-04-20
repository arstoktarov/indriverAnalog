<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnicCharacteristics;
use Illuminate\Http\Request;

class TechnicCharacteristicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $characteristics = TechnicCharacteristics::orderBy('id', 'desc')->paginate(10);

        return view('admin.characteristic.index', ['characteristics' => $characteristics]);
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
     * @param  \App\Models\TechnicCharacteristics  $technicCharacteristics
     * @return \Illuminate\Http\Response
     */
    public function show(TechnicCharacteristics $technicCharacteristics)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TechnicCharacteristics  $technicCharacteristics
     * @return \Illuminate\Http\Response
     */
    public function edit(TechnicCharacteristics $technicCharacteristics)
    {
        return view('admin.characteristic.index', ['characteristics' => $technicCharacteristics]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TechnicCharacteristics  $technicCharacteristics
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TechnicCharacteristics $technicCharacteristics)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TechnicCharacteristics  $technicCharacteristics
     * @return \Illuminate\Http\Response
     */
    public function destroy(TechnicCharacteristics $technicCharacteristics)
    {
        //
    }
}
