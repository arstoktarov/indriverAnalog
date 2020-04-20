<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialType;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $materials = Material::orderBy('id', 'desc')->paginate(10);
        $types = MaterialType::orderBy('id', 'desc')->get();
        return view('admin.material.index', ['materials' => $materials, 'types' => $types]);
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
        $material = new Material();
        $material->type_id = $request['type_id'];
        $material->title = $request['title'];
        $material->brand = $request['brand'];
        $material->description = $request['description'];
        if($request['image']){
            $material->avatar = $this->uploadFile($request['image']);
        }
        $material->save();
        return back()->withMessage('Успешно');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function show(Material $material)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function edit(Material $material)
    {
        return view('admin.material.edit', ['material' => $material]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Material $material)
    {
        $material->type_id = $request['type_id'];
        $material->title = $request['title'];
        $material->brand = $request['brand'];
        $material->description = $request['description'];
        if ($request->file('image')) {
            if (!is_null($material['avatar'])) {
                $this->deleteFile($material['avatar']);
            }
            $material['avatar'] = $this->uploadFile($request['image']);
        }
        $material->save();
        return redirect($request['redirects_to'] ?? route('materials.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function destroy(Material $material)
    {
        $material->delete();
        return back()->withMessage('Успешно удалено!');
    }
}
