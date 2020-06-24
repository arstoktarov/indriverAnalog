<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\City;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where('type', 2)->with('technics', 'materials')->orderBy('id', 'desc')->paginate(10);
        $cities = City::get();
        if($request['phone']) {
            $users = User::where('type', 2)->where('phone', 'like', '%'.$request['phone'].'%')->with('technics', 'materials')->orderBy('id', 'desc')->paginate(10);
        }
        return view('admin.user.index', ['users' => $users, 'cities' => $cities]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $phone = User::where('phone', $request['phone'])->first();
        if($phone) return back()->withError('Пользователь с таким номером существует');
        $user = new User();
        $user->type = 2;
        $user->name = $request['first_name'];
        $user->phone = $request['phone'];
        $user->city_id = $request['city_id'];
        $user->password = $request['password'];
        $user->balance = $request['balance'];
        $user->phone_verification_code = 1234;
        $user->token = Str::random(30);
        $user->push = 1;
        $user->sound = 1;
        $user->lang = 'ru';
        $user->save();

        return back()->withMessage('Успешно!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load([
            'userTechnic',
            'userTechnic.technic',
            'userTechnic.technic.type',
            'userMaterials.material',
            'userMaterials.material.type',
            'userMaterials'
        ]);
        $transactions = Transaction::where('user_id', $user->id)->paginate(20);
        return view('admin.user.show', ['user' => $user, 'transactions' => $transactions]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
//        $user->load([
//            'technics'
//        ]);
        $cities = City::get();
        return view('admin.user.edit', ['user' => $user, 'cities' => $cities]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->balance = $request['balance'];
        $user->save();
        return redirect($request['redirects_to'] ?? route('cities.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return back()->withMessage('Успешно удалено!');
    }
}
