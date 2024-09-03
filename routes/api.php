<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Inventory\AttributeController;
use App\Http\Controllers\Inventory\BrandController;
use App\Http\Controllers\Inventory\CategoryController;
use App\Http\Controllers\Inventory\ColorController;
use App\Http\Controllers\Inventory\Setting\ProductMinimumOrderController;
use App\Http\Controllers\Inventory\Setting\ProductShippingClassController;
use App\Http\Controllers\Inventory\SizeController;
use App\Http\Controllers\Inventory\TagController;
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

Route::group(['prefix' => 'inventory','middleware' => 'auth:sanctum'], function(){
    Route::group(['prefix' => 'products'], function(){

        Route::group(['prefix' => 'brands'], function(){
            Route::get('/',  [ BrandController::class , 'fetchBrand']);
            Route::post('/',  [ BrandController::class , 'store']);
            Route::post('/update',  [ BrandController::class , 'update']);
        });

        Route::group(['prefix' => 'attributes'], function(){
            Route::get('/',  [ AttributeController::class , 'fetchAttributes']);
            Route::post('/',  [ AttributeController::class , 'store']);
            Route::post('/update',  [ AttributeController::class , 'update']);
        });

        Route::group(['prefix' => 'categories'], function(){
            Route::get('/',  [ CategoryController::class , 'fetchCategories']);
            Route::post('/',  [ CategoryController::class , 'store']);
            Route::post('/update',  [ CategoryController::class , 'update']);
        });

        Route::group(['prefix' => 'colors'], function(){
            Route::get('/',  [ ColorController::class , 'fetchColors']);
            Route::post('/',  [ ColorController::class , 'store']);
            Route::post('/update',  [ ColorController::class , 'update']);
        });

        Route::group(['prefix' => 'sizes'], function(){
            Route::get('/',  [ SizeController::class , 'fetchSizes']);
            Route::post('/',  [ SizeController::class , 'store']);
            Route::post('/update',  [ SizeController::class , 'update']);
        });

        Route::group(['prefix' => 'tags'], function(){
            Route::get('/',  [ TagController::class , 'fetchTags']);
            Route::post('/',  [ TagController::class , 'store']);
            Route::post('/update',  [ TagController::class , 'update']);
        });

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

//Http Exception
Route::any('{path}', function() {
    return response()->json([
        'status'        => 'error',
        'statusMessage' => 'Route not found',
        'httpCode'      => '404',
        'errorCode'     => '9002',
        'response'      => ''
    ], 404);
})->where('path', '.*');

