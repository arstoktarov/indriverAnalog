<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});




Route::get('/admin/sign-in', 'Admin\MainController@viewSignIn')->name('viewSignIn');
Route::post('/admin/sign-in', 'Admin\MainController@signIn')->name('signIn');
Route::get('/admin/sign-out', 'Admin\MainController@signOut')->name('signOut');


Route::group(['prefix' => '/admin', 'namespace' => 'Admin', 'middleware' => 'accessAdmin'], function () {

    Route::get('/main', 'MainController@viewIndex')->name('main');

    Route::resource('users', 'UserController');
    Route::resource('cities', 'CityController');
    Route::resource('materials', 'MaterialController');
    Route::resource('materialTypes', 'MaterialTypeController');
    Route::resource('technics', 'TechnicController');
    Route::resource('technicCategories', 'TechnicCategoryController');
    Route::resource('technicCharacteristics', 'TechnicCharacteristicController');
    Route::resource('settings', 'SettingController');

});















