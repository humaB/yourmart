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
use App\Http\Controllers\Inventory\Store\StoreInwardController;
use App\Http\Controllers\Inventory\Order\OrderController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\PurchaseOrder\InventoryPurchaseOrderController;
use App\Http\Controllers\Pages\PageController;
use App\Http\Controllers\Pages\LibraryPageController;
use App\Http\Controllers\Pages\HelpCenterPageController;
use App\Http\Controllers\User\DropShipperController;
use App\Http\Controllers\User\SupplierController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\AccountHeadController;
use App\Http\Controllers\Account\BankTransactionController;
use App\Http\Controllers\Account\CashTransactionController;
use App\Http\Controllers\Account\JournalTransactionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\Account\Report\FinanceReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Helpers\LeopardApiHelper;
use App\Http\Controllers\Inventory\Setting\ProductOtherChargesController;
use App\Http\Controllers\Inventory\Store\CourierReturnController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::get('/get-profile', [LoginController::class , 'getProfile']);
    Route::post('/update-profile', [LoginController::class , 'updateProfile']);
});

Route::group(['prefix' => 'users','middleware' => 'auth:sanctum'], function(){
    Route::get('/',  [ UserController::class , 'getUsers']);
    Route::post('/',  [ UserController::class , 'store']);
    Route::post('/update',  [ UserController::class , 'update']);
    Route::post('/delete',  [ UserController::class , 'delete']);
});

Route::group(['prefix' => 'tickets'], function(){
    Route::post('/', [TicketController::class, 'fetchTickets']);
    Route::get('/status-counts', [TicketController::class, 'getTicketStatusCounts']);
    Route::post('/particular', [TicketController::class, 'getTicket']);

    Route::group(['prefix' => 'messages'], function(){
        Route::post('/add', [TicketController::class, 'storeMessage']);
        Route::post('/particular', [TicketController::class, 'getMessages']);
    });
});

Route::post('tickets/messages/upload/image', [TicketController::class, 'apiImage']); //this will be open route for now


Route::group(['prefix' => 'dropshippers','middleware' => 'auth:sanctum'], function(){
    Route::get('/',  [ DropShipperController::class , 'getRequests']);
    Route::post('/',  [ DropShipperController::class , 'store']);
    Route::post('/details',  [ DropShipperController::class , 'fetchDetails']);
    Route::post('/decisions',  [ DropShipperController::class , 'decision']);
    Route::get('/orders',  [ DropShipperController::class , 'orders']);
    Route::post('/shops/payments',  [ DropShipperController::class , 'shopPayments']);

    Route::group(['prefix' => 'payments'], function(){
        Route::post('/data',  [ DropShipperController::class , 'paymentData']);
        Route::post('/add',  [ DropShipperController::class , 'addPayment']);
    });
});

Route::group(['prefix' => 'pages','middleware' => 'auth:sanctum'], function(){
    Route::group(['prefix' => 'settings'], function(){
        Route::get('/home-page',  [ PageController::class , 'fectHomePageSettingStore']);
        Route::post('/home-page',  [ PageController::class , 'homePageSettingStore']);

        Route::group(['prefix' => 'library-page'], function(){
            Route::get('/',  [ LibraryPageController::class , 'fetchCourses']);
            Route::post('/',  [ LibraryPageController::class , 'store']);
            Route::post('/update',  [ LibraryPageController::class , 'update']);
            Route::post('/delete',  [ LibraryPageController::class , 'delete']);
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
        Route::get('/complete-drop-down', [ ProductController::class , 'completeDropDown']);

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

        Route::post('/tags/removes', [ ProductController::class , 'removeTag']);

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
            Route::post('/revert',  [ OrderController::class , 'revert']);
            Route::post('/reject',  [ OrderController::class , 'reject']);

            Route::post('/update-paid-amount',  [ OrderController::class , 'updatePaidAmount']);
            Route::post('/update-packaging-amount',  [ OrderController::class , 'updatePackagingAmount']);

            Route::post('/tracking',  [ OrderController::class , 'trackingDetails']);
        });

        Route::group(['prefix' => 'settings'], function(){
            Route::get('/minimum-order-quantities',  [ ProductMinimumOrderController::class , 'fetchHistory']);
            Route::post('/minimum-order-quantities',  [ ProductMinimumOrderController::class , 'store']);

            Route::get('/other-charges',  [ ProductOtherChargesController::class , 'fetchHistory']);
            Route::post('/other-charges',  [ ProductOtherChargesController::class , 'store']);


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
            Route::post('/stocks/update-barcode',  [ StoreInwardController::class , 'updateBarcode']);

            //Returns
            Route::get('/pending-returns',  [ CourierReturnController::class , 'pendingReturns']);
            Route::get('/product-returned',  [ CourierReturnController::class , 'inWardRecord']);
            Route::post('/product-returned',  [ CourierReturnController::class , 'returnProduct']);
        });
    });
});

Route::post('/web-hook/leopard',  function( Request $request ){
    $leopard = new LeopardApiHelper();
    Log::info($request);
    return $leopard->webHook($request);
});

Route::prefix('accounts')->group(function () {

    Route::get('/{first}/second', [AccountController::class,'secondLevelOfFirst']);

    Route::prefix('groups')->group(function () {
        Route::post('/add', [AccountController::class,'groupStore']);
        Route::get('/', [AccountController::class,'accountGroups']);
        Route::get('/{second}/third', [AccountController::class,'thirdLevelOfSecond']);
        Route::get('/{third}/fourth', [AccountController::class,'fourthLevelOfThird']);
        Route::post('/update', [AccountController::class,'groupUpdate']);
    });

    Route::prefix('heads')->group(function () {
        Route::post('/add', [AccountHeadController::class,'headStore']);
        Route::get('/', [AccountHeadController::class,'accountHeads']);
        Route::post('/update', [AccountHeadController::class,'headUpdate']);

        Route::prefix('banks')->group(function () {
            Route::post('/add', [AccountHeadController::class,'headBankStore']);
            Route::get('/', [AccountHeadController::class,'accountHeadBanks']);
            Route::post('/update', [AccountHeadController::class,'headBankUpdate']);
        });

        Route::prefix('cash')->group(function () {
            Route::post('/add', [AccountHeadController::class,'headCashStore']);
            Route::get('/', [AccountHeadController::class,'accountHeadCash']);
            Route::post('/update', [AccountHeadController::class,'headCashUpdate']);
        });
    });

    // Transactions
    Route::prefix('transactions')->group(function () {

        Route::prefix('bank-transactions')->group(function () {
            Route::post('/add', [BankTransactionController::class,'bankTransactionAdd']);
            Route::get('/', [BankTransactionController::class,'bankTransactions']);
            Route::post('/edit', [BankTransactionController::class,'bankTransaction']);
            Route::post('/show', [BankTransactionController::class,'bankTransactionDetail']);
            Route::post('/do/approve', [BankTransactionController::class,'approveBankTransaction']);
            Route::post('/update', [BankTransactionController::class,'bankTransactionUpdate']);
        });

        Route::prefix('cash-transactions')->group(function () {
            Route::post('/add', [CashTransactionController::class,'cashTransactionAdd']);
            Route::get('/', [CashTransactionController::class,'cashTransactions']);
            Route::post('/edit', [CashTransactionController::class,'cashTransaction']);
            Route::post('/show', [CashTransactionController::class,'cashTransactionDetail']);
            Route::post('/do/approve', [CashTransactionController::class,'approveCashTransaction']);
            Route::post('/update', [CashTransactionController::class,'cashTransactionUpdate']);
        });

        Route::prefix('journal-transactions')->group(function () {
            Route::post('/add', [JournalTransactionController::class,'journalTransactionAdd']);
            Route::get('/', [JournalTransactionController::class,'journalTransactions']);
            Route::post('/edit', [JournalTransactionController::class,'journalTransaction']);
            Route::post('/show', [JournalTransactionController::class,'journalTransactionDetail']);
            Route::post('/do/approve', [JournalTransactionController::class,'approveJournalTransaction']);
            Route::post('/update', [JournalTransactionController::class,'journalTransactionUpdate']);
        });
    });

    Route::prefix('reports')->group(function () {
        Route::get('/helper/data', [FinanceReportController::class,'helperData']);
        Route::prefix('finance')->group(function () {
            Route::post('/receipts', [FinanceReportController::class,'receiptReport']);
            Route::post('/general/ledger', [FinanceReportController::class,'generalLedgerReport']);
            Route::post('/ledger', [FinanceReportController::class,'ledgerReport']);
            Route::post('/general/journal', [FinanceReportController::class,'journalReport']);
            Route::post('/trial/sheet', [FinanceReportController::class,'trialSheetReport']);
            Route::post('/daily/report', [FinanceReportController::class,'dailyReport']);
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

