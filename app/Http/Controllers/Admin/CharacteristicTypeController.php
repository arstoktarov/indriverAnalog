<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CharacteristicType;
use Illuminate\Http\Request;

class CharacteristicTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = CharacteristicType::orderBy('title')->paginate(10);

        return view('admin.cType.index', ['types' => $types]);
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
        $type = new CharacteristicType();
        $type->title = $request['title'];
        $type->save();

        return back()->withMessage('Успешно!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CharacteristicType  $characteristicType
     * @return \Illuminate\Http\Response
     */
    public function show(CharacteristicType $characteristicType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CharacteristicType  $characteristicType
     * @return \Illuminate\Http\Response
     */
    public function edit(CharacteristicType $characterType)
    {
        return view('admin.cType.edit', ['characterType' => $characterType]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CharacteristicType  $characteristicType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CharacteristicType $characterType)
    {
        $characterType->title = $request['title'];
        $characterType->save();
        return redirect($request['redirects_to'] ?? route('technicCharacteristics.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CharacteristicType  $characteristicType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CharacteristicType $characterType)
    {
        $characterType->delete();
        return back()->withMessage('Успешно!');
    }
}
