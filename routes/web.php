<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Inventory\Gate\GateInWardController;
use App\Http\Controllers\Inventory\Gate\StoreInwardController;
use App\Http\Controllers\Inventory\Order\OrderController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\PurchaseOrder\InventoryPurchaseOrderController;
use App\Http\Controllers\Inventory\Setting\ProductMinimumOrderController;
use App\Http\Controllers\Inventory\Setting\ProductShippingClassController;
use App\Http\Controllers\Inventory\Setting\CourierController;
use App\Http\Controllers\Pages\PageController;
use App\Http\Controllers\Pages\LibraryPageController;
use App\Http\Controllers\User\DropShipperController;
use App\Http\Controllers\User\SupplierController;
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

Route::group(['prefix' => '/pages', 'middleware' => 'auth'], function () {
    Route::get('/', [PageController::class, 'index'])->name('pages');
    Route::get('/library', [LibraryPageController::class, 'index'])->name('library.page');

});


Route::group(['prefix' => '/inventory', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => '/products'], function () {
        Route::get('/', [ProductController::class, 'index'])->name('inventory.products');
        Route::group(['prefix' => '/settings'], function () {
            Route::get('/minimum-order-quantity', [ProductMinimumOrderController::class, 'index'])->name('inventory.products.moq');
            Route::get('/shipping-classes', [ProductShippingClassController::class, 'index'])->name('inventory.products.shipping_classes');
        });

        Route::group(['prefix' => '/orders'], function () {
            Route::get('/', [OrderController::class, 'index'])->name('inventory.products.orders');
        });

        Route::group(['prefix' => '/purchase-orders'], function () {
            Route::get('/', [InventoryPurchaseOrderController::class, 'index'])->name('inventory.products.purchase_orders');
            Route::post('/pdf', [InventoryPurchaseOrderController::class, 'pdf']);
            Route::get('/requests', [InventoryPurchaseOrderController::class, 'requests'])->name('inventory.products.purchase_orders.requests');
        });

        Route::group(['prefix' => '/gate'], function () {
            Route::get('/purchase-orders', [GateInWardController::class, 'index'])->name('inventory.products.gate.purchase_orders');
            Route::get('/inward-records', [GateInWardController::class, 'record'])->name('inventory.products.gate.record');
            Route::post('/inward-record/pdf', [GateInWardController::class, 'pdf']);
        });

        Route::group(['prefix' => '/store'], function () {
            Route::get('/purchase-orders', [StoreInwardController::class, 'index'])->name('inventory.products.store.purchase_orders');
            Route::get('/inward-records', [StoreInwardController::class, 'record'])->name('inventory.products.store.record');
            Route::post('/inward-record/pdf', [StoreInwardController::class, 'pdf']);

            Route::get('/stock', [StoreInwardController::class, 'stock'])->name('inventory.products.store.stock');
        });
    });
});

Route::group(['prefix' => '/requests', 'middleware' => 'auth'], function () {
    Route::get('/dropshippers', [DropShipperController::class, 'index'])->name('request.dropshipper');
    Route::post('/dropshippers/pdf', [DropShipperController::class, 'pdf']);

    Route::group(['prefix' => '/orders'], function () {
        Route::get('/', [DropShipperController::class, 'orderIndex'])->name('inventory.products.dropshipper.orders');
    });

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('request.supplier');
    Route::post('/suppliers/pdf', [SupplierController::class, 'pdf']);

});

Route::group(['prefix' => '/couriers', 'middleware' => 'auth'], function () {
    Route::get('/', [CourierController::class, 'index'])->name('couriers');

});

