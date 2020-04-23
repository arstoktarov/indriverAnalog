<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnicCharacteristics;
use App\Models\CharacteristicType;
use Illuminate\Http\Request;

class TechnicCharacteristicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request['id'])
        {
            $characteristics = TechnicCharacteristics::where('technic_id', $request['id'])->with('type')->orderBy('id', 'desc')->paginate(10);
            $types = CharacteristicType::orderBy('title')->get();
            return view('admin.characteristic.index', ['characteristics' => $characteristics, 'types' => $types, 'id' => $request['id']]);
        }
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
        $category = new TechnicCharacteristics();
        $category->type_id = $request['type_id'];
        $category->technic_id = $request['technic_id'];
        $category->title = $request['title'];
        $category->value = $request['value'];
        $category->unit = $request['unit'];
        $category->save();

        return back()->withMessage('Успешно!');
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
    public function edit(TechnicCharacteristics $technicCharacteristic)
    {
        $types = CharacteristicType::orderBy('title')->get();
        return view('admin.characteristic.edit', ['technicCharacteristic' => $technicCharacteristic, 'types' => $types]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TechnicCharacteristics  $technicCharacteristics
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TechnicCharacteristics $technicCharacteristic)
    {
        $technicCharacteristic['type_id'] = $request['type_id'];
        $technicCharacteristic['title'] = $request['title'];
        $technicCharacteristic['value'] = $request['value'];
        $technicCharacteristic['unit'] = $request['unit'];
        $technicCharacteristic['technic_id'] = $technicCharacteristic['technic_id'];
        $technicCharacteristic->save();
        return redirect($request['redirects_to'] ?? route('technicCharacteristics.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TechnicCharacteristics  $technicCharacteristics
     * @return \Illuminate\Http\Response
     */
    public function destroy(TechnicCharacteristics $technicCharacteristics)
    {
        $technicCharacteristics->delete();
        return back()->withMessage('Успешно!');
    }
}
