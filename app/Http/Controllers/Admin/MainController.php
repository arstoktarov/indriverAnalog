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
        return view('admin.index');
    }
}
