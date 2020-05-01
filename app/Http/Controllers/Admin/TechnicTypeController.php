<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnicType;
use Illuminate\Http\Request;

class TechnicTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = TechnicType::orderBy('title')->paginate(10);

        return view('admin.tType.index', ['types' => $types]);
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
        $type = new TechnicType();
        $type->title = $request['title'];
        $type->description = $request['description'];
        $type->charac_title = $request['charac_title'];
        $type->charac_unit = $request['charac_unit'];
        if($request['image']){
            $type->image = $this->uploadFile($request['image']);
        }
        $type->save();
        return back()->withMessage('Успешно!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TechnicType  $technicType
     * @return \Illuminate\Http\Response
     */
    public function show(TechnicType $technicType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TechnicType  $technicType
     * @return \Illuminate\Http\Response
     */
    public function edit(TechnicType $technicType)
    {
        return view('admin.tType.edit', ['technicType' => $technicType]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TechnicType  $technicType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TechnicType $technicType)
    {
        $technicType->title = $request['title'];
        $technicType->description = $request['description'];
        $technicType->charac_title = $request['charac_title'];
        $technicType->charac_unit = $request['charac_unit'];
        if ($request->file('image')) {
            if (!is_null($technicType['image'])) {
                $this->deleteFile($technicType['image']);
            }
            $technicType['image'] = $this->uploadFile($request['image']);
        }
        $technicType->save();
        return redirect($request['redirects_to'] ?? route('technicTypes.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TechnicType  $technicType
     * @return \Illuminate\Http\Response
     */
    public function destroy(TechnicType $technicType)
    {
        $technicType->delete();
        return back()->withMessage('Успешно удалено!');
    }
}
