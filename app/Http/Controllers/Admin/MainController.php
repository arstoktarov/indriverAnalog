<?php
/**
 * Created by PhpStorm.
 * User: madiy
 * Date: 12.10.2019
 * Time: 16:47
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\City;
use App\Models\Material;
use App\Models\TechnicType;
use App\Models\MaterialType;
use App\Models\Technic;
use App\Models\TechnicCategory;
use App\Models\CharacteristicType;
use App\Models\User;

/**
 * Class MainController
 * @package App\Http\Controllers\Admin
 */
class MainController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewSignIn()
    {
        return view('admin.sign_in');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signIn(Request $request)
    {
        $rules = [
            'username' => 'required|exists:admins,username',
            'password' => 'required'
        ];

        $admin = Admin::where('username', $request['username'])
            ->where('password', $request['password'])
            ->first();
        if (isset($admin)) {
            /*if (isset($admin->type) && $admin->type == Admin::TYPE_ADMIN) {
                session()->put('vK68TF23TfYKYDBZSCC9', 1);
                session()->put('admin', $admin);
                session()->save();
                return redirect()->route('viewIndex');
            }
            else {
                session()->put('D670GZ1TbTou6A4eymXg', 1);
                session()->put('admin', $admin);
                session()->save();
                return redirect()->route('viewIndex');
            }*/
            session()->put('vK68TF23TfYKYDBZSCC9', 1);
            session()->put('admin', $admin);
            session()->save();
            return redirect()->route('main');
        } else {
            return back()->withErrors('Неправильный пароль или логин');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function signOut(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect()->route('viewSignIn');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewIndex()
    {
        $data['city'] = City::get()->count();
        $data['mType'] = MaterialType::get()->count();
        $data['tType'] = TechnicType::get()->count();
        $data['users'] = User::get()->count();
        return view('admin.index', ['data' => $data]);
    }
}
