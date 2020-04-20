<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechnicCategory;
use Illuminate\Http\Request;

class TechnicCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = TechnicCategory::orderBy('id', 'desc')->paginate(10);

        return view('admin.category.index', ['categories' => $categories]);
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
        $category = new TechnicCategory();
        $category->title = $request['title'];
        $category->save();

        return back()->withMessage('Успешно создано');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TechnicCategory  $technicCategory
     * @return \Illuminate\Http\Response
     */
    public function show(TechnicCategory $technicCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TechnicCategory  $technicCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(TechnicCategory $technicCategory)
    {
        return view('admin.category.edit', ['category' => $technicCategory]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TechnicCategory  $technicCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TechnicCategory $technicCategory)
    {
        $technicCategory->title = $request['title'];
        $technicCategory->save();

        return redirect($request['redirects_to'] ?? route('technicCategories.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TechnicCategory  $technicCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(TechnicCategory $technicCategory)
    {
        $technicCategory->delete();
        return back()->withMessage('Успешно удалено!');
    }
}
