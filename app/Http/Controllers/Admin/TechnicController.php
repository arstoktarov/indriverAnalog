<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technic;
use App\Models\TechnicCategory;
use Illuminate\Http\Request;

class TechnicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $technics = Technic::with('category')->orderBy('id', 'desc')->paginate(10);
        $categories = TechnicCategory::orderBy('id', 'desc')->get();

        return view('admin.technic.index', ['technics' => $technics, 'categories' => $categories]);
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
        $technic = new Technic();
        $technic->category_id = $request['category_id'];
        $technic->model = $request['model'];
        $technic->specification = $request['specification'];
        if($request['image']){
            $technic->image = $this->uploadFile($request['image'], 'technics');
        }
        $technic->save();

        return back()->withMessage('Успешно создано!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Technic  $technic
     * @return \Illuminate\Http\Response
     */
    public function show(Technic $technic)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Technic  $technic
     * @return \Illuminate\Http\Response
     */
    public function edit(Technic $technic)
    {
        $categories = TechnicCategory::orderBy('id', 'desc')->get();
        return view('admin.technic.edit', ['technic' => $technic, 'categories' => $categories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Technic  $technic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Technic $technic)
    {
        $technic->category_id = $request['category_id'];
        $technic->model = $request['model'];
        $technic->specification = $request['specification'];
        if ($request->file('image')) {
            if (!is_null($technic['path'])) {
                $this->deleteFile($technic['image']);
            }
            $technic['image'] = $this->uploadFile($request['image']);
        }
        $technic->save();
        return redirect($request['redirects_to'] ?? route('technics.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Technic  $technic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Technic $technic)
    {
        $technic->delete();
        return back()->withMessage('Успешно удалено!');
    }
}
