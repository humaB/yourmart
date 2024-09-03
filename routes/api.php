<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Inventory\Setting\ProductMinimumOrderController;
use App\Http\Controllers\Inventory\Setting\ProductShippingClassController;
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

// Route::get('/',  [ AuthController::class , 'me'])->middleware('JwtToken');
// Route::post('/generate_token',  [ AuthController::class , 'getToken']);

// Route::group(['prefix' => 'users',  'middleware' => 'JwtToken'], function(){
//     Route::get('/',  [ UserController::class , 'getUsers']);
//     Route::post('/',  [ UserController::class , 'userStore']);
// });


Route::get('test', function(){
    return "Hello";
});


Route::get('test2', function(){
    return "Hello";
})->middleware('auth:sanctum');

Route::group(['prefix' => 'inventory','middleware' => 'auth:sanctum'], function(){
    Route::group(['prefix' => 'products'], function(){
        Route::group(['prefix' => 'settings'], function(){
            Route::get('/minimum-order-quantities',  [ ProductMinimumOrderController::class , 'fetchHistory']);
            Route::post('/minimum-order-quantities',  [ ProductMinimumOrderController::class , 'store']);

            Route::get('/shipping-classes',  [ ProductShippingClassController::class , 'fetchRecord']);
            Route::post('/shipping-classes',  [ ProductShippingClassController::class , 'store']);
            Route::post('/shipping-classes/edit',  [ ProductShippingClassController::class , 'update']);
            Route::post('/shipping-classes/details',  [ ProductShippingClassController::class , 'details']);
            Route::post('/shipping-classes/edit-details',  [ ProductShippingClassController::class , 'editDetails']);
            Route::post('/shipping-classes/change-status',  [ ProductShippingClassController::class , 'changeStatus']);

        });
    });
});
