<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\Setting\ProductMinimumOrderController;
use App\Http\Controllers\Inventory\Setting\ProductShippingClassController;
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

Route::get('/', [LoginController::class, 'index'])->name('dashboard');
Route::post('/login', [LoginController::class, 'login'])->name('login');


Route::group(['prefix' => '/', 'middleware' => 'auth'], function () {
    Route::get('generate_token', [AuthController::class, 'getToken']);
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
});

//*******************************************
//            Users
//*******************************************

Route::group(['prefix' => '/users', 'middleware' => 'auth'], function () {
    Route::get('/', [UserController::class, 'index'])->name('user');
    Route::get('/create', [UserController::class, 'create'])->name('user.add');
    Route::post('/store', [UserController::class, 'store'])->name('user.store');
});


Route::group(['prefix' => '/inventory', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => '/products'], function () {
        Route::get('/', [ProductController::class, 'index'])->name('inventory.products');
        Route::group(['prefix' => '/settings'], function () {
            Route::get('/minimum-order-quantity', [ProductMinimumOrderController::class, 'index'])->name('inventory.products.moq');
            Route::get('/shipping-classes', [ProductShippingClassController::class, 'index'])->name('inventory.products.shipping_classes');
        });
    });
});

