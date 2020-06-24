<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});

Route::group(['namespace' => 'v1\Rest', 'prefix' => 'v1'], function() {

    Route::get('cities', 'CityController@index');

    Route::group(['prefix' => 'user'], function() {
        
        Route::post('sign-up', 'UserController@signUp');
        Route::post('sign-in', 'UserController@signIn');
        Route::post('resendCode', 'UserController@resendCode');
        Route::post('verify', 'UserController@verify');
        Route::post('password/reset', 'UserController@resetPassword');
        Route::post('password/reset/check', 'UserController@checkResetPasswordCode');
        Route::post('password/change', 'UserController@changePassword');
        Route::post('create', 'UserController@createUser');

        Route::group(['middleware' => 'userAuth:executor'], function() {
            Route::get('materials/orders/list', 'MaterialOrderController@ordersList');
        });

        Route::group(['middleware' => 'userAuth'], function() {

            Route::post('push', 'UserController@sendPush');
            Route::post('edit', 'UserController@editProfile');
            Route::post('edit/type', 'UserController@changeType');
            Route::get('auth', 'UserController@auth');
            Route::post('balance/add', 'PaymentController@addToBalance')->name('payBalance');

            Route::get('technics/orders', 'TechnicOrderController@index');
            Route::get('materials/orders', 'MaterialOrderController@index');
            Route::get('technics/orders/{id}', 'TechnicOrderController@show');
        });

        Route::group(['middleware' => 'userAuth:user'], function() {
            Route::post('materials/orders', 'MaterialOrderController@create');
            Route::post('materials/orders/responses/choose', 'MaterialOrderController@chooseExecutor');
            Route::post('materials/orders/responses/decline', 'MaterialOrderController@declineExecutor');
            Route::get('materials/orders/responses/{id}', 'MaterialOrderController@responses');
        });

        Route::group(['middleware' => 'userAuth:executor'], function() {

            Route::post('technics/add', 'TechnicController@addTechnic');
            Route::get('technics', 'TechnicController@userTechnics');
            Route::delete('technics/{id}', 'TechnicController@deleteTechnic');

            Route::post('materials/add', 'MaterialController@addMaterial');
            Route::get('materials', 'MaterialController@userMaterials');
            Route::delete('materials/{id}', 'MaterialController@deleteMaterial');

            Route::post('materials/orders/responses', 'MaterialOrderController@createResponse');

        });
    });

    Route::get('materials', 'MaterialController@index');
    Route::get('materials/types', 'MaterialController@types');
    Route::get('technics', 'TechnicController@index');
    Route::get('technics/types', 'TechnicController@types');
    Route::get('settings', 'SettingController@index');

    //Route::any('payment/result', 'PaymentController@paymentResult')->name('paymentResult');
    Route::post('payment/result', 'PaymentController@paymentResult')->name('paymentResult');
    Route::get('payment/success', 'PaymentController@paymentSuccess')->name('paymentSuccess');
    Route::get('payment/fail', 'PaymentController@paymentFail')->name('paymentFail');
});
