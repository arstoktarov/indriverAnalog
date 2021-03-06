<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialType;
use Illuminate\Http\Request;

class MaterialTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $types = MaterialType::orderBy('title')->paginate(10);

        return view('admin.mType.index', ['types' => $types]);
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
        $type = new MaterialType();
        $type->title = $request['title'];
        $type->description = $request['description'];
        $type->charac_title = $request['charac_title'];
        $type->charac_unit = $request['charac_unit'];
        if($request['image']){
            $type->image = $this->uploadFile($request['image'], 'material');
        }
        $type->save();

        return back()->withMessage('Успешно!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MaterialType  $materialType
     * @return \Illuminate\Http\Response
     */
    public function show(MaterialType $materialType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MaterialType  $materialType
     * @return \Illuminate\Http\Response
     */
    public function edit(MaterialType $materialType)
    {
        return view('admin.mType.edit', ['type' => $materialType]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MaterialType  $materialType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MaterialType $materialType)
    {
        $materialType->title = $request['title'];
        $materialType->description = $request['description'];
        $materialType->charac_title = $request['charac_title'];
        $materialType->charac_unit = $request['charac_unit'];
        if ($request->file('image')) {
            if (!is_null($materialType['image'])) {
                $this->deleteFile($materialType['image']);
            }
            $materialType['image'] = $this->uploadFile($request['image'], 'material');
        }
        $materialType->save();
        return redirect($request['redirects_to'] ?? route('materialTypes.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MaterialType  $materialType
     * @return \Illuminate\Http\Response
     */
    public function destroy(MaterialType $materialType)
    {
        $materialType->delete();
        return back()->withMessage('Успешно!');
    }
}
