<?php

use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Inventory\AttachmentController;
use App\Http\Controllers\Inventory\Attributes\AttributeController;
use App\Http\Controllers\Inventory\Attributes\BrandController;
use App\Http\Controllers\Inventory\Attributes\CategoryController;
use App\Http\Controllers\Inventory\Attributes\ColorController;
use App\Http\Controllers\Inventory\Setting\ProductMinimumOrderController;
use App\Http\Controllers\Inventory\Setting\ProductShippingClassController;
use App\Http\Controllers\Inventory\Setting\CourierController;
use App\Http\Controllers\Inventory\Attributes\SizeController;
use App\Http\Controllers\Inventory\Attributes\TagController;
use App\Http\Controllers\Inventory\Gate\GateInWardController;
use App\Http\Controllers\Inventory\Setting\ProductPackagingClassController;
use App\Http\Controllers\Inventory\Gate\StoreInwardController;
use App\Http\Controllers\Inventory\Order\OrderController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\PurchaseOrder\InventoryPurchaseOrderController;
use App\Http\Controllers\Pages\PageController;
use App\Http\Controllers\Pages\LibraryPageController;
use App\Http\Controllers\Pages\HelpCenterPageController;
use App\Http\Controllers\User\DropShipperController;
use App\Http\Controllers\User\SupplierController;
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

Route::group(['prefix' => 'users','middleware' => 'auth:sanctum'], function(){
    Route::get('/',  [ UserController::class , 'getUsers']);
    Route::post('/',  [ UserController::class , 'store']);
    Route::post('/update',  [ UserController::class , 'update']);
});


Route::group(['prefix' => 'dropshippers','middleware' => 'auth:sanctum'], function(){
    Route::get('/',  [ DropShipperController::class , 'getRequests']);
    Route::post('/',  [ DropShipperController::class , 'store']);
    Route::post('/details',  [ DropShipperController::class , 'fetchDetails']);
    Route::post('/decisions',  [ DropShipperController::class , 'decision']);

    Route::get('/orders',  [ DropShipperController::class , 'orders']);
});

Route::group(['prefix' => 'pages','middleware' => 'auth:sanctum'], function(){
    Route::group(['prefix' => 'settings'], function(){
        Route::get('/home-page',  [ PageController::class , 'fectHomePageSettingStore']);
        Route::post('/home-page',  [ PageController::class , 'homePageSettingStore']);

        Route::group(['prefix' => 'library-page'], function(){
            Route::get('/',  [ LibraryPageController::class , 'fectLibraryPageSettingStore']);
            Route::post('/add',  [ LibraryPageController::class , 'libraryPageSettingStore']);
            Route::post('/update',  [ LibraryPageController::class , 'libraryPageSettingUpdate']);
        });
        Route::group(['prefix' => 'help-center-page'], function(){
            Route::get('/',  [ HelpCenterPageController::class , 'fectHelpCenterPageSetting']);
            Route::post('/add',  [ HelpCenterPageController::class , 'helpCenterPageSettingStore']);
            Route::post('/update',  [ HelpCenterPageController::class , 'helpCenterPageSettingUpdate']);
        });
    });
});


Route::group(['prefix' => 'suppliers','middleware' => 'auth:sanctum'], function(){
    Route::get('/',  [ SupplierController::class , 'getRequests']);

    Route::get('/drop-down',  [ SupplierController::class , 'dropDown']);
    Route::post('/',  [ SupplierController::class , 'store']);
    Route::post('/details',  [ SupplierController::class , 'fetchDetails']);
    Route::post('/decisions',  [ SupplierController::class , 'decision']);
});

Route::group(['prefix' => 'couriers','middleware' => 'auth:sanctum'], function(){
    Route::get('/', [CourierController::class, 'couriers']); // Fetch all couriers
    Route::post('/add', [CourierController::class, 'store']); // Add a new courier
    Route::post('/update', [CourierController::class, 'update']); // Update an existing courier

    Route::post('/details', [CourierController::class, 'details']);

    Route::get('/categories', [CourierController::class, 'fetchCategory']);
    Route::post('/categories', [CourierController::class, 'addCategory']);
    Route::post('/categories/update', [CourierController::class, 'updateCategory']);

    Route::post('/categories/ranges', [CourierController::class, 'fetchCategoryRanges']);
});

Route::group(['prefix' => 'inventory','middleware' => 'auth:sanctum'], function(){

    Route::group(['prefix' => 'products'], function(){

        Route::get('/',  [ ProductController::class , 'fetchProducts']);
        Route::post('/',  [ ProductController::class , 'store']);
        Route::post('/update', [ ProductController::class , 'update']);
        Route::post('/details', [ ProductController::class , 'details']);
        Route::post('/change-statuses', [ ProductController::class , 'changeStatus']);
        Route::post('/filter-data', [ ProductController::class , 'filterData']);

        Route::post('/drop-down', [ ProductController::class , 'dropDown']);
        Route::post('/variations/update', [ ProductController::class , 'variationUpdate']);
        Route::post('/variations/delete-images', [ ProductController::class , 'variationDeleteImage']);
        Route::post('/variations/change-status', [ ProductController::class , 'variationChangeStatus']);

        Route::post('/clone',  [ ProductController::class , 'cloneProduct']);

        Route::post('/discounts/changed', [ ProductController::class , 'discountChanged']);
        Route::post('/dimensions/changed', [ ProductController::class , 'dimensionsChanged']);

        Route::post('/up-sells/changed', [ ProductController::class , 'updateUpSells']);
        Route::post('/tags/changed', [ ProductController::class , 'updateTags']);
        Route::post('/status/changed', [ ProductController::class , 'updateStatus']);
        Route::post('/hero-image/changed', [ ProductController::class , 'updateHeroImage']);
        Route::post('/color-images/changed', [ ProductController::class , 'updateColorImages']);

        Route::group(['prefix' => 'attachments'], function(){
            Route::get('/',  [ AttachmentController::class , 'fetchAttachments']);
            Route::post('/',  [ AttachmentController::class , 'store']);

            Route::post('/update',  [ AttachmentController::class , 'update']);
            Route::post('/delete',  [ AttachmentController::class , 'delete']);
        });

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


        Route::group(['prefix' => 'orders'], function(){
            Route::get('/',  [ OrderController::class , 'fetchOrders']);
            Route::post('/details',  [ OrderController::class , 'details']);
            Route::post('/comments',  [ OrderController::class , 'comment']);

            Route::post('/update-status',  [ OrderController::class , 'updateStatus']);
        });

        Route::group(['prefix' => 'settings'], function(){
            Route::get('/minimum-order-quantities',  [ ProductMinimumOrderController::class , 'fetchHistory']);
            Route::post('/minimum-order-quantities',  [ ProductMinimumOrderController::class , 'store']);

        
            Route::group(['prefix' => 'packaging-classes'], function(){
                Route::get('/',  [ ProductPackagingClassController::class , 'fectPackagingClassSetting']);
                Route::post('/add',  [ ProductPackagingClassController::class , 'packagingClassSettingStore']);
                Route::post('/update',  [ ProductPackagingClassController::class , 'packagingClassSettingUpdate']);
                Route::get('/drop-down',  [ ProductPackagingClassController::class , 'dropDown']);
            });

            Route::get('/shipping-classes',  [ ProductShippingClassController::class , 'fetchRecord']);
            Route::get('/shipping-classes/drop-down',  [ ProductShippingClassController::class , 'dropDown']);
            Route::post('/shipping-classes',  [ ProductShippingClassController::class , 'store']);
            Route::post('/shipping-classes/edit',  [ ProductShippingClassController::class , 'update']);
            Route::post('/shipping-classes/details',  [ ProductShippingClassController::class , 'details']);
            Route::post('/shipping-classes/edit-details',  [ ProductShippingClassController::class , 'editDetails']);
            Route::post('/shipping-classes/change-status',  [ ProductShippingClassController::class , 'changeStatus']);
        });

        Route::group(['prefix' => 'purchase-orders'], function(){
            Route::get('/',  [ InventoryPurchaseOrderController::class , 'fetchRecord']);
            Route::post('/',  [ InventoryPurchaseOrderController::class , 'store']);
            Route::post('/decisions',  [ InventoryPurchaseOrderController::class , 'decisions']);
        });

        Route::group(['prefix' => 'gate'], function(){
            Route::get('/pending-purchase-orders',  [ GateInWardController::class , 'pendingPO']);

            Route::get('/product-inwards',  [ GateInWardController::class , 'inWardRecord']);
            Route::post('/product-inward',  [ GateInWardController::class , 'inWard']);
        });

        Route::group(['prefix' => 'store'], function(){
            Route::get('/pending-purchase-orders',  [ StoreInwardController::class , 'pendingPO']);

            Route::get('/product-inwards',  [ StoreInwardController::class , 'inWardRecord']);
            Route::post('/product-inward',  [ StoreInwardController::class , 'inWard']);

            Route::get('/stocks',  [ StoreInwardController::class , 'fetchStock']);
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

