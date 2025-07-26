<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\Inventory\Gate\GateInWardController;
use App\Http\Controllers\Inventory\Store\StoreInwardController;
use App\Http\Controllers\Inventory\Order\OrderController;
use App\Http\Controllers\Inventory\ProductController;
use App\Http\Controllers\Inventory\PurchaseOrder\InventoryPurchaseOrderController;
use App\Http\Controllers\Inventory\Setting\ProductMinimumOrderController;
use App\Http\Controllers\Inventory\Setting\ProductShippingClassController;
use App\Http\Controllers\Inventory\Setting\ProductPackagingClassController;
use App\Http\Controllers\Inventory\Setting\CourierController;
use App\Http\Controllers\Pages\PageController;
use App\Http\Controllers\Pages\LibraryPageController;
use App\Http\Controllers\Pages\HelpCenterPageController;
use App\Http\Controllers\User\DropShipperController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\User\SupplierController;
use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\AccountHeadController;
use App\Http\Controllers\Account\BankTransactionController;
use App\Http\Controllers\Account\CashTransactionController;
use App\Http\Controllers\Account\JournalTransactionController;
use App\Http\Controllers\Account\Report\FinanceReportController;
use App\Http\Controllers\Account\pdf\TransactionPdfController;
use App\Http\Controllers\Inventory\Setting\ProductOtherChargesController;
use App\Http\Controllers\Inventory\Store\CourierReturnController;
use App\Http\Controllers\Inventory\Store\StoreCheckOutController;
use App\Http\Controllers\Pages\EmailTemplateController;
use App\Http\Controllers\Pages\NotificationController;
use App\Http\Controllers\Report\FisReportController;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\Store\StoreReturnDetail;
use App\Models\User\Supplier;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
Route::get('profile-settings', [LoginController::class, 'profileSettingIndex'])->name('profile.setting');


Route::group(['prefix' => '/', 'middleware' => 'auth'], function () {
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

//*******************************************
//            Reports
//*******************************************
Route::group(['prefix' => '/reports', 'middleware' => 'auth'], function () {
    Route::get('/fis', [FisReportController::class, 'index'])->name('reports.fis');
});

Route::group(['prefix' => '/tickets', 'middleware' => 'auth'], function () {
    Route::get('/', [TicketController::class, 'index'])->name('tickets');
});

Route::group(['prefix' => '/pages', 'middleware' => 'auth'], function () {
    Route::get('/', [PageController::class, 'index'])->name('pages');
    Route::get('/library', [LibraryPageController::class, 'index'])->name('library.page');
    Route::get('/help-center', [HelpCenterPageController::class, 'index'])->name('help.center.page');
    Route::get('/dropshipper-settings', [LibraryPageController::class, 'dropshipperIndex'])->name('pages.dropshipper');
});


Route::group(['prefix' => '/inventory', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => '/products'], function () {
        Route::get('/', [ProductController::class, 'index'])->name('inventory.products');
        Route::group(['prefix' => '/settings'], function () {
            Route::get('/minimum-order-quantity', [ProductMinimumOrderController::class, 'index'])->name('inventory.products.moq');
            Route::get('/shipping-classes', [ProductShippingClassController::class, 'index'])->name('inventory.products.shipping_classes');
            Route::get('/packaging-classes', [ProductPackagingClassController::class, 'index'])->name('packaging.class');

            Route::get('/other-charges', [ProductOtherChargesController::class, 'index'])->name('inventory.products.other_charges');

        });

        Route::group(['prefix' => '/orders'], function () {
            Route::get('/', [OrderController::class, 'index'])->name('inventory.products.orders');
            Route::get('/records', [OrderController::class, 'record'])->name('inventory.products.order_record');

            Route::get('/dispatchs', [OrderController::class, 'dispatchIndex'])->name('inventory.products.orders.dispatchs');

            Route::post('/postex-airbill', [OrderController::class, 'postExAirbill']);
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



            Route::group(['prefix' => '/returns'], function () {
                Route::get('/couriers', [CourierReturnController::class, 'index'])->name('inventory.products.store.returns');
                Route::get('/couriers/records', [CourierReturnController::class, 'record'])->name('inventory.products.store.return_record');
            });

            Route::group(['prefix' => '/check-outs'], function () {
                Route::get('/', [StoreCheckOutController::class, 'index'])->name('inventory.products.store.check_out');
                Route::get('/records', [StoreCheckOutController::class, 'record'])->name('inventory.products.check_out.return_record');
                Route::post('/pdf', [StoreCheckOutController::class, 'pdf']);
            });

            Route::get('/stock', [StoreInwardController::class, 'stock'])->name('inventory.products.store.stock');
        });
    });
});

Route::group(['prefix' => '/dropshippers', 'middleware' => 'auth'], function () {
    Route::get('/pay-outs', [DropShipperController::class, 'payOuts'])->name('dropshipper.payouts');
    Route::post('/payment-history', [DropShipperController::class, 'payment_receipt'])->name('dropshipper.payment_receipt');
    Route::post('/ledger', [DropShipperController::class, 'payment_ledger'])->name('dropshipper.payment_ledger');

    Route::get('/preview', [DropShipperController::class, 'preview'])->name('dropshipper.preview');

});

Route::get('/suppliers/pay-outs', [SupplierController::class, 'payOuts'])->name('supplier.payouts');

Route::group(['prefix' => '/requests', 'middleware' => 'auth'], function () {
    Route::get('/dropshippers', [DropShipperController::class, 'index'])->name('request.dropshipper');
    Route::post('/dropshippers/pdf', [DropShipperController::class, 'pdf']);

    Route::group(['prefix' => '/orders'], function () {
        Route::get('/', [DropShipperController::class, 'orderIndex'])->name('inventory.products.dropshipper.orders');
    });

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('request.supplier');

    Route::post('/suppliers/pdf', [SupplierController::class, 'pdf']);

});

// this is just for preview pages
Route::group(['prefix' => '/accounts', 'middleware' => 'auth'], function () {
    Route::get('/groups', [AccountController::class, 'groupIndex'])->name('account.group');
    Route::get('/heads', [AccountHeadController::class, 'headIndex'])->name('account.head');
    Route::get('/heads/banks', [AccountHeadController::class, 'headBankIndex'])->name('account.head.bank');
    Route::get('/heads/cash', [AccountHeadController::class, 'headCashIndex'])->name('account.head.cash');
    Route::get('/transactions/bank-transactions', [BankTransactionController::class, 'bankTransactionIndex'])->name('account.transaction.bank.transactions');
    Route::get('/transactions/cash-transactions', [CashTransactionController::class, 'cashTransactionIndex'])->name('account.transaction.cash.transactions');
    Route::get('/transactions/journal-transactions', [JournalTransactionController::class, 'journalTransactionIndex'])->name('account.transaction.journal.transactions');
    Route::get('/reports/finance', [FinanceReportController::class, 'reportIndex'])->name('account.report.finance');

});
// this is functional route that will show pdf etc
Route::prefix('accounts')->group(function () {
    // Transactions
    Route::prefix('transactions')->group(function () {

        Route::post('/pdf', [TransactionPdfController::class,'transactionPdf']);
        Route::post('receipts/pdf', [TransactionPdfController::class,'receiptPdf']);
        Route::post('general/ledger/pdf', [TransactionPdfController::class,'generalLedgerPdf']);
        Route::post('ledger/pdf', [TransactionPdfController::class,'ledgerPdf']);
        Route::post('general/journal/pdf', [TransactionPdfController::class,'journalPdf']);
        Route::post('general/trial/pdf', [TransactionPdfController::class,'generalTrialPdf']);
        Route::post('daily/report/pdf', [TransactionPdfController::class,'dailyReportPdf']);
    });


});


Route::group(['middleware' => 'auth'], function () {
    Route::get('/couriers', [CourierController::class, 'index'])->name('couriers');
    Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email_template');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notification');
});


use App\Models\User;
Route::get('/update-supplier', function(){
    $users = User::where('role', 'supplier')->get();

    foreach( $users as $user){
       Supplier::where('email', $user->email)->update([
            'user_id' => $user->id
       ]);
    }
});
