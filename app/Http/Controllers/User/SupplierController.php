<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Account\Helper\AccountHeadHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\NotificationHelper;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Mail\SupplierDecisionMail;
use App\Models\Account\AccountHead;
use App\Models\Account\AccountTransaction;
use App\Models\Account\Bank;
use App\Models\Account\Cash;
use App\Models\CustomerBank;
use App\Models\Inventory\Order\OrderItemSupplier;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\PurchaseOrder\PurchaseOrder;
use App\Models\Inventory\Store\StoreIssuance;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReceived;
use App\Models\Inventory\Store\StoreReceivedDetail;
use App\Models\Setting\EmailTemplate;
use App\Models\Setting\Notification;
use App\Models\User;
use App\Models\User\DropShipper;
use App\Models\User\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use TCPDF;

include(public_path() . '/assets/tcpdf/tcpdf.php');

class SupplierController extends Controller
{
    public function index()
    {
        return view('user.supplier');
    }

    public function dashboard()
    {
        return view('user.supplier_dashboard');
    }

    public function payOuts()
    {
        return view('user.supplier_payout');
    }

    public function getRequests()
    {

        $suppliers = Supplier::orderBy('id', 'desc')->get();

        return (new ResponseCollection($suppliers))
            ->response()
            ->setStatusCode(200);
    }

    public function dropDown()
    {

        $suppliers = Supplier::select('id as code', 'full_name as label')->where('status', '1')->get();

        return (new ResponseCollection($suppliers))
            ->response()
            ->setStatusCode(200);
    }

    public function fetchData(Request $request)
    {

        $supplierId = $request->supplier['code'] == 0 ? null : $request->supplier['code'];
        $from = $request->from;
        $to   = $request->to;

        // ---------------- SUPPLIER STOCK ----------------
        $supplierReceived = StoreReceivedDetail::when(
            isset($supplierId),
            fn($q) => $q->where('supplier_id', $supplierId),
            fn($q) => $q->where('supplier_id', '>', 0)
        )
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->whereHas('grn.purchase_order', fn($q) => $q->where('supplier_stock', '1'))
            ->get();

        $supplierReceivedValue = $supplierReceived->sum('total');

        $inprocess = OrderItemSupplier::with('order')
            ->when(
                isset($supplierId),
                fn($q) => $q->where('supplier_id', $supplierId),
                fn($q) => $q->where('supplier_id', '>', 0)
            )
            ->whereHas('order', function ($query) {
                $query->whereNotIn('status', ['6', '7', '8', '9', '10', '12']);
            })
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->get();

        $totalInprocessValue = 0;
        foreach ($inprocess as $inprocess) {
            $product = StoreReceivedDetail::when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
                ->whereNotNull('supplier_id')
                ->where('product_id', $inprocess->product_id)
                ->get();

            //Get Average Price
            $avg_price = round($product->sum('total') / $product->sum('quantity'));

            $totalInprocessValue += (float)$avg_price * (float)$inprocess->quantity;
        }

        // Supplier Issued (sold out)
        $supplierSold = OrderItemSupplier::with('order')
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->whereHas('order', fn($q) => $q->where('status', '8'))
            ->get();

        $supplierIssuedValue = 0;
        foreach ($supplierSold as $sold) {
            $product = StoreReceivedDetail::when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                ->where('product_id', $sold->product_id)
                ->get();

            $avg_price = $product->sum('quantity') > 0
                ? round($product->sum('total') / $product->sum('quantity'))
                : 0;

            $supplierIssuedValue += (float) $avg_price * (float) $sold->quantity;
        }

        // Supplier Payments
        $grn = StoreReceived::when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->pluck('po_id');

        $purchase_orders = PurchaseOrder::whereIn('id', $grn)
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->where('status', '1')
            ->where('supplier_stock', '1')
            ->get();

        $supplierPaid = $purchase_orders->sum('total_amount') - $purchase_orders->sum('remaining_amount');


        // ---------------- YOURMART STOCK ----------------
        $yourmartReceived = StoreReceivedDetail::when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->whereHas('grn.purchase_order', fn($q) => $q->where('supplier_stock', '0'))
            ->get();

        $yourmartReceivedValue = $yourmartReceived->sum('total');

        // Issued from YourMart (exclude already in OrderItemSupplier)
        $orderIdsUsedInSupplier = OrderItemSupplier::pluck('order_id');

        $yourmartIssued = StoreIssuanceDetail::whereHas('sin.order', function ($q) use ($orderIdsUsedInSupplier) {
            $q->where('status', '8')
                ->whereNotIn('id', $orderIdsUsedInSupplier); // exclude supplier-linked orders
        })
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->get();

        $yourmartIssuedValue = $yourmartIssued->sum('total');

        // Yourmart Payments
        $grn = StoreReceived::when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->pluck('po_id');

        $purchase_orders = PurchaseOrder::whereIn('id', $grn)
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->where('status', '1')
            ->where('supplier_stock', '0')
            ->get();

        $yourmartPaid = $purchase_orders->sum('total_amount') - $purchase_orders->sum('remaining_amount');



        // ---------------- OVERALL ----------------
        $overallReceived = $supplierReceivedValue + $yourmartReceivedValue;
        $overallIssued   = $supplierIssuedValue + $yourmartIssuedValue;
        $overallPaid     = $supplierPaid + $yourmartPaid; // YourMart has no supplier-paid concept
        $overallBalance  = $overallIssued - $overallPaid;


        // ---------------- RESPONSE ----------------
        $data = [
            // Supplier Stock
            'supplierReceived' => $supplierReceivedValue,
            'supplierIssued'   => $supplierIssuedValue,
            'supplierPaid'     => $supplierPaid,
            'supplierBalance'  => $supplierIssuedValue - $supplierPaid,
            'supplierTotalInprocessValue' => $totalInprocessValue,

            // YourMart Stock
            'yourmartReceived' => $yourmartReceivedValue,
            'yourmartIssued'   => $yourmartIssuedValue,
            'yourmartPaid'     => $yourmartPaid,
            'yourmartBalance'  => $yourmartIssuedValue - $yourmartPaid,

            // Overall
            'overallReceived'  => $overallReceived,
            'overallIssued'    => $overallIssued,
            'overallPaid'      => $overallPaid,
            'overallBalance'   => $overallBalance,
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }


    public function inTake(Request $request)
    {
        $supplierId = $request->supplier['code'] ?? null;
        $from       = $request->from;
        $to         = $request->to;

        $status = $request->status; // "In Stock", "In Process", "Sold Out"

        if ($status != '') {
            return $this->filterByStatus($status, $supplierId, $from, $to, $request);
        }

        // ---------------- SUPPLIER STOCK ----------------
        $supplierReceived = StoreReceivedDetail::with(['product.variation'])
            ->when(
                !empty($supplierId) && $supplierId !== '0',
                fn($q) => $q->where('supplier_id', $supplierId),
                fn($q) => $q->where('supplier_id', '>', 0)
            )
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->whereHas('grn.purchase_order', fn($q) => $q->where('supplier_stock', '1'))
            ->get()
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                $total_qty    = $items->sum('quantity');
                $total_amount = $items->sum('total');
                $latest_price = $total_qty ? round($total_amount / $total_qty) : 0;

                return [
                    'product_id'     => $productId,
                    'product_name'   => $items->first()->product->title ?? '',
                    'hero_image'     => $items->first()->product->hero_image ?? '',
                    'slug'           => $items->first()->product->slug ?? '',
                    'sku'            => $items->first()->product->variation->sku ?? '',
                    'stock_in_qty'   => $total_qty,
                    'stock_in_price' => $latest_price,
                    'stock_in_amount' => $total_qty * $latest_price,
                ];
            });

        // ✅ In Process
        $inprocess = OrderItemSupplier::with('order')
            ->when(
                !empty($supplierId) && $supplierId !== '0',
                fn($q) => $q->where('supplier_id', $supplierId),
                fn($q) => $q->where('supplier_id', '>', 0)
            )
            ->whereHas(
                'order',
                fn($query) =>
                $query->whereNotIn('status', ['6', '7', '8', '9', '10', '12'])
            )
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->get()
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                return [
                    'inprocess_qty'    => $items->sum('quantity'),
                    'inprocess_amount' => $items->sum(fn($i) => $i->quantity * $i->price),
                ];
            });

        $supplierSold = OrderItemSupplier::with(['order', 'product'])
            ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->whereHas('order', fn($q) => $q->where('status', '8'))
            ->get()
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                $total_qty = $items->sum('quantity');
                $amount    = $items->sum(fn($i) => $i->quantity * $i->product->variation->avg_price);

                return [
                    'product_id'       => $productId,
                    'sold_out_qty'     => $total_qty,
                    'sold_out_amount'  => $amount,
                ];
            });

        $supplierFinal = $supplierReceived->map(function ($item) use ($supplierSold, $inprocess) {
            $productId = $item['product_id'];

            $soldItem = $supplierSold->get($productId);
            $inprocessItem = $inprocess->get($productId);

            $sold_qty    = $soldItem['sold_out_qty'] ?? 0;
            $sold_amount = $soldItem['sold_out_amount'] ?? 0;

            $inprocess_qty    = $inprocessItem['inprocess_qty'] ?? 0;
            $inprocess_amount = $inprocessItem['inprocess_amount'] ?? 0;

            $balance_qty = $item['stock_in_qty'] - $sold_qty - $inprocess_qty;
            $balance_amount = $balance_qty * $item['stock_in_price'];

            return [
                'product_name'     => $item['product_name'],
                'hero_image'       => $item['hero_image'],
                'slug'             => $item['slug'],
                'sku'              => $item['sku'],
                'stock_in_qty'     => $item['stock_in_qty'],
                'stock_in_price'   => $item['stock_in_price'],
                'stock_in_amount'  => $item['stock_in_amount'],

                'inprocess_qty'    => $inprocess_qty,
                'inprocess_amount' => $inprocess_amount,

                'sold_out_qty'     => $sold_qty,
                'sold_out_amount'  => $sold_amount,

                'balance_qty'      => $balance_qty,
                'balance_amount'   => $balance_amount,

                'po' => null, // placeholder
            ];
        })->values();

        // ---------------- YOURMART STOCK ----------------
        $yourmartReceived = StoreReceivedDetail::with(['product.variation'])
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->whereHas('grn.purchase_order', fn($q) => $q->where('supplier_stock', '0'))
            ->get()
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                $total_qty    = $items->sum('quantity');
                $total_amount = $items->sum('total');
                $latest_price = $total_qty ? round($total_amount / $total_qty) : 0;

                return [
                    'product_id'       => $productId,
                    'product_name'     => $items->first()->product->title ?? '',
                    'slug'             => $items->first()->product->slug ?? '',
                    'sku'              => $items->first()->product->variation->sku ?? '',
                    'stock_in_qty'     => $total_qty,
                    'stock_in_price'   => $latest_price,
                    'stock_in_amount'  => $total_qty * $latest_price,
                ];
            });

        $excludeOrders = OrderItemSupplier::pluck('order_id');

        $yourmartIssued = StoreIssuanceDetail::with(['product.variation', 'sin.order'])
            ->whereHas('sin.order', function ($q) use ($excludeOrders) {
                $q->where('status', '8')
                    ->whereNotIn('id', $excludeOrders);
            })
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->get()
            ->groupBy('product_id')
            ->map(function ($items, $productId) {
                $total_qty = $items->sum('quantity');
                $amount    = $items->sum('total');

                return [
                    'product_id'       => $productId,
                    'sold_out_qty'     => $total_qty,
                    'sold_out_amount'  => $amount,
                ];
            });

        $yourmartFinal = $yourmartReceived->map(function ($item) use ($yourmartIssued) {
            $productId   = $item['product_id'];
            $soldItem    = $yourmartIssued->get($productId);

            $sold_qty    = $soldItem['sold_out_qty'] ?? 0;
            $sold_amount = $soldItem['sold_out_amount'] ?? 0;

            $balance_qty    = $item['stock_in_qty'] - $sold_qty;
            $balance_amount = $balance_qty * $item['stock_in_price'];

            return [
                'product_name'     => $item['product_name'],
                'slug'             => $item['slug'],
                'sku'              => $item['sku'],
                'stock_in_qty'     => $item['stock_in_qty'],
                'stock_in_price'   => $item['stock_in_price'],
                'stock_in_amount'  => $item['stock_in_amount'],
                'sold_out_qty'     => $sold_qty,
                'sold_out_amount'  => $sold_amount,
                'balance_qty'      => $balance_qty,
                'balance_amount'   => $balance_amount,
                'source'           => 'YourMart',
            ];
        })->values();;

        // ---------------- OVERALL ----------------
        $overall = $supplierFinal->merge($yourmartFinal)
            ->groupBy('sku')
            ->map(function ($items) {
                $first = $items->first();

                $stock_in_qty    = $items->sum('stock_in_qty');
                $stock_in_amount = $items->sum('stock_in_amount');
                $sold_qty        = $items->sum('sold_out_qty');
                $sold_amount     = $items->sum('sold_out_amount');
                $inprocess_qty   = $items->sum('inprocess_qty');      // ✅ added
                $inprocess_amt   = $items->sum('inprocess_amount');   // ✅ added
                $balance_qty     = $items->sum('balance_qty');
                $balance_amount  = $items->sum('balance_amount');

                $stock_in_price  = $stock_in_qty > 0 ? round($stock_in_amount / $stock_in_qty) : 0;

                return [
                    'product_name'     => $first['product_name'],
                    'slug'             => $first['slug'],
                    'sku'              => $first['sku'],
                    'stock_in_qty'     => $stock_in_qty,
                    'stock_in_price'   => $stock_in_price,
                    'stock_in_amount'  => $stock_in_amount,

                    'inprocess_qty'    => $inprocess_qty,       // ✅ included
                    'inprocess_amount' => $inprocess_amt,       // ✅ included

                    'sold_out_qty'     => $sold_qty,
                    'sold_out_amount'  => $sold_amount,

                    'balance_qty'      => $balance_qty,
                    'balance_amount'   => $balance_amount,
                    'source'           => 'Overall',
                ];
            })->values();

        // ---------------- FINAL RESPONSE ----------------
        $final = [
            'supplier' => $supplierFinal->values(),
            'yourmart' => $yourmartFinal->values(),
            'overall'  => $overall,
        ];

        return (new ResponseCollection($final))
            ->response()
            ->setStatusCode(200);
    }

    public function filterByStatus($status, $supplierId, $from, $to, $request)
    {
        if ($status === 'In Stock') {
            $final = StoreReceivedDetail::with(['product.variation'])
                ->when(
                    !empty($supplierId) && $supplierId !== '0',
                    fn($q) => $q->where('supplier_id', $supplierId),
                    fn($q) => $q->where('supplier_id', '>', 0)
                )
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                ->whereHas('grn.purchase_order', fn($q) => $q->where('supplier_stock', '1'))
                ->get()
                ->groupBy('product_id')
                ->map(function ($items, $productId) {
                    $total_qty    = $items->sum('quantity');
                    $total_amount = $items->sum('total');
                    $latest_price = $total_qty ? round($total_amount / $total_qty) : 0;

                    return [
                        'product_id'     => $productId,
                        'product_name'   => $items->first()->product->title ?? '',
                        'hero_image'     => $items->first()->product->hero_image ?? '',
                        'slug'           => $items->first()->product->slug ?? '',
                        'sku'            => $items->first()->product->variation->sku ?? '',
                        'stock_in_qty'   => $total_qty,
                        'stock_in_price' => $latest_price,
                        'stock_in_amount' => $total_qty * $latest_price,
                    ];
                });
        }
        if ($status == 'In Process') {

            // ✅ In Process
            $final = OrderItemSupplier::with('order')
                ->when(
                    !empty($supplierId) && $supplierId !== '0',
                    fn($q) => $q->where('supplier_id', $supplierId),
                    fn($q) => $q->where('supplier_id', '>', 0)
                )
                ->whereHas(
                    'order',
                    fn($query) =>
                    $query->whereNotIn('status', ['6', '7', '8', '9', '10', '12'])
                )
                ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
                ->when($request->to, fn($q) => $q->whereDate('created_at', '<=', $request->to))
                ->get()
                ->groupBy('product_id')
                ->map(function ($items, $productId) use ($supplierId, $from, $to) {
                    $product = Product::find($productId);

                    $received = StoreReceivedDetail::when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
                        ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                        ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                        ->where('product_id', $productId)
                        ->whereNotNull('supplier_id')
                        ->get();

                    $avg_price = $received->sum('quantity') > 0
                        ? round($received->sum('total') / $received->sum('quantity'))
                        : 0;

                    return [
                        'product_name'     => $product->title,
                        'hero_image'       => $product->hero_image,
                        'slug'             => $product->slug,
                        'sku'              => $product->variation->sku,
                        'stock_in_qty'     => 0,
                        'stock_in_price'   => $avg_price,
                        'stock_in_amount'  => 0,

                        'inprocess_qty'    => $items->sum('quantity'),
                        'inprocess_amount' => $items->sum('quantity') * $avg_price,

                        'sold_out_qty'     => 0,
                        'sold_out_amount'  => 0,

                        'balance_qty'      => 0,
                        'balance_amount'   => 0,

                        'po' => null, // placeholder
                    ];
                });
        }
        if ($status == 'Sold Out') {
            $final = OrderItemSupplier::with(['order', 'product'])
                ->when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
                ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                ->whereHas('order', fn($q) => $q->where('status', '8'))
                ->get()
                ->groupBy('product_id')
                ->map(function ($items, $productId) use ($supplierId, $from, $to) {
                    $product = Product::find($productId);

                    $received = StoreReceivedDetail::when($supplierId, fn($q) => $q->where('supplier_id', $supplierId))
                        ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
                        ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
                        ->where('product_id', $productId)
                        ->whereNotNull('supplier_id')
                        ->get();

                    $avg_price = $received->sum('quantity') > 0
                        ? round($received->sum('total') / $received->sum('quantity'))
                        : 0;

                    return [
                        'product_name'     => $product->title,
                        'hero_image'       => $product->hero_image,
                        'slug'             => $product->slug,
                        'sku'              => $product->variation->sku,
                        'stock_in_qty'     => 0,
                        'stock_in_price'   => $avg_price,
                        'stock_in_amount'  => 0,

                        'inprocess_qty'    => 0,
                        'inprocess_amount' => 0,

                        'sold_out_qty'     => $items->sum('quantity'),
                        'sold_out_amount'  => $items->sum('quantity') * $avg_price,

                        'balance_qty'      => 0,
                        'balance_amount'   => 0,

                        'po' => null, // placeholder
                    ];
                });
        }

        $final = [
            'supplier' => $final->values(),
        ];
        return (new ResponseCollection($final))
            ->response()
            ->setStatusCode(200);
    }


    public function purchaseOrders(Request $request)
    {

        $supplierId = $request->supplier['code'] ?? null;
        $from = $request->from;
        $to = $request->to;

        $product = Product::where('slug', $request->slug)->first();

        $purchaseOrdersQuery = PurchaseOrder::with('details.product')
            ->where('status', '1')
            ->when($supplierId, function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId);
            })
            ->when($from, fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('created_at', '<=', $to))
            ->whereHas('details', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            });

        // Handle type conditions
        if ($request->type == 'supplier') {
            // Supplier-specific
            $purchaseOrdersQuery->where('supplier_stock', '1');
        } elseif ($request->type == 'yourmart') {
            // Yourmart only
            $purchaseOrdersQuery->where('supplier_stock', '0');
        }

        $purchase_orders = $purchaseOrdersQuery->get();

        return (new ResponseCollection($purchase_orders))
            ->response()
            ->setStatusCode(200);
    }

    public function fetchDetails(Request $request)
    {

        $suppliers = Supplier::with('bank', 'city', 'shops', 'products')->where('id', $request->id)->get();

        return (new ResponseCollection($suppliers))
            ->response()
            ->setStatusCode(200);
    }

    public function update(Request $request)
    {
        // Update supplier Information
        $supplier = Supplier::findOrFail($request->id);

        if ($supplier->email != $request->input('email')) {
            //Check if email is already registered or not for status approved
            $user = User::where('email', $request->input('email'))->first();
            if ($user) {
                return (new ValidationCollection(["This Email already registered with another account"]))
                    ->response()
                    ->setStatusCode(400);
            }
        }

        if ($request->filled('changedPassword')) {
            User::where('id', $supplier->user_id)->update([
                'password' => Hash::make($request->input('changedPassword')),
            ]);
        }

        User::where('id', $supplier->user_id)->update([
            'email'   => $request->input('email'),
        ]);

        $customerBank = CustomerBank::firstOrCreate(
            ['name' => $request->bank['name']], // Conditions to check
        );

        $supplier->update([
            'full_name'       => $request->input('full_name'),
            'email'           => $request->input('email'),
            'cnic_number'     => $request->input('cnic_number'),
            'whatsapp_number' => $request->input('whatsapp_number'),
            'address'         => $request->input('address'),
            'bank_id'         => $customerBank->id,
            'account_number'  => $request->input('account_number'),
            'account_title'   => $request->input('account_title'),
            'account_iban'    => $request->input('account_iban'),
            'payment_cycle'   => $request->input('payment_cycle')
        ]);

        return response()->json(['message' => 'Dropshipper information updated successfully.']);
    }

    public function decision(Request $request)
    {

        $supplier = Supplier::where('id', $request->id)->first();

        if ($request->action != 'reject') {
            $user = User::create([
                'name'     => $supplier->full_name,
                'email'    => $supplier->email,
                'password' => $supplier->password,
                'role'     => 'supplier',
                'allowed_ip_address' => '*'
            ]);
        }

        $supplier->update([
            'user_id' => $user->id ?? 0,
            'status'  => $request->action == 'reject' ? '2' : '1' // 0 => Pending | 1 => Approved | 2 => Rejected
        ]);

        // Prepare the data
        $mailData = [
            'request'         => $supplier->id,
            'full_name'       => $supplier->full_name,
            'whatsapp_number' => $supplier->whatsapp_number,
            'address'         => $supplier->address,
            'decision'        => $request->action
        ];

        $template = EmailTemplate::where('type', $request->action == 'reject' ? 'supplier_application_rejected' : 'supplier_application_approved')->first();

        Mail::to($supplier->email)->send(new SupplierDecisionMail($mailData, $template));

        return ['message', 'successfully updated'];
    }

    public function pendingSupplierPayment()
    {
        $suppliers = PurchaseOrder::select(
            'supplier_id',
            DB::raw('SUM(total_amount) as total_order_amount'),
            DB::raw('SUM(remaining_amount) as total_remaining_amount')
        )
            ->where('status', '1')
            ->where('supplier_stock', '1')
            ->groupBy('supplier_id')
            ->having('total_remaining_amount', '>', 0)
            ->orderByDesc('supplier_id')
            ->get();


        // Group purchase orders by supplier_id where supplier_stock = 0 and status = 1
         // ---------------- PURCHASE ORDER FINANCIALS ----------------
       $poFinancials = PurchaseOrder::select(
            'supplier_id',
            DB::raw('SUM(total_amount) as total_order_amount'),
            DB::raw('SUM(remaining_amount) as total_remaining_amount')
        )
            ->where('status', '1')       // Assuming '1' means the PO is active/open
            ->where('supplier_stock', '1') // Only POs linked to supplier-managed stock
            ->groupBy('supplier_id')
            ->having('total_remaining_amount', '>', 0)
            ->orderByDesc('supplier_id')
            ->get()
            // Convert the Collection to a keyed map for fast lookup in the final loop
            ->keyBy('supplier_id');

        // ---------------- SUPPLIER STOCK ----------------
        $supplierReceived = StoreReceivedDetail::whereHas('grn.purchase_order', fn($q) => $q->where('supplier_stock', '1'))
            ->get();

        // Supplier Issued (sold out)
        $supplierSold = OrderItemSupplier::with('order')
            ->whereHas('order', fn($q) => $q->where('status', '8'))
            ->get();

        $supplierIssuedValue = 0;
        foreach ($supplierSold as $sold) {
            $product = StoreReceivedDetail::where('product_id', $sold->product_id)
                ->get();

            $avg_price = $product->sum('quantity') > 0
                ? round($product->sum('total') / $product->sum('quantity'))
                : 0;

            $supplierIssuedValue += (float) $avg_price * (float) $sold->quantity;
        }

        $totalPayable = $suppliers->sum('total_order_amount');
        $totalRemaining = $suppliers->sum('total_remaining_amount');
        $totalPaid = $totalPayable - $totalRemaining;
        $remainingDropshippers = $suppliers->count();
        $supplierReceivedValue = $supplierReceived->sum('total');


        // ---------------- SUPPLIER STOCK ----------------
        $supplierReceived = StoreReceivedDetail::with('supplier')
            ->whereHas('grn.purchase_order', fn($q) => $q->where('supplier_stock', '1'))
            ->whereNotNull('supplier_id')
            ->get()
            ->groupBy('supplier_id')
            ->filter(fn($items, $supplierId) => $supplierId !== null) // Added filter after groupBy for robustness
            ->map(function ($items, $supplierId) {
                $total_qty    = $items->sum('quantity');
                $total_amount = $items->sum('total');
                $latest_price = $total_qty ? round($total_amount / $total_qty, 2) : 0;

                $supplier = $items->first()->supplier ?? (object)['id' => $supplierId, 'name' => 'Unknown', 'email' => '', 'whatsapp_number' => ''];

                return [
                    'name'            => $supplier->full_name ?? '',
                    'email'           => $supplier->email ?? '',
                    'contact'         => $supplier->whatsapp_number ?? '',
                    'supplier_id'     => $supplier->id,
                    'stock_in_qty'    => (int) $total_qty,
                    'stock_in_price'  => (float) $latest_price,
                    'stock_in_amount' => (float) ($total_qty * $latest_price),
                ];
            });

        // ---
        // ✅ In Process
       $inprocess = OrderItemSupplier::with('order','product.variation')
            ->whereHas(
                'order',
                fn($query) =>
                $query->whereNotIn('status', ['6', '7', '8', '9', '10', '12'])
            )
            ->get()
            ->groupBy('supplier_id')
            ->map(function ($items, $supplierId) {
                $total_qty = $items->sum('quantity');

                // Access the price from the 'product' relationship
                $total_amount = $items->sum(fn($i) =>
                    // Access product relationship, casting for safety
                    (float) $i->quantity * (float) optional($i->product->variation)->avg_price
                );

                return [
                    'supplier_id'      => (int) $supplierId,
                    'inprocess_qty'    => (int) $total_qty,
                    'inprocess_amount' => (float) $total_amount,
                ];
            });
        // ---
        // 💸 Supplier Sold
        $supplierSold = OrderItemSupplier::with(['order', 'product.variation'])
            // Added direct filter on the table column 'supplier_id'
            ->whereNotNull('supplier_id')
            ->whereHas('order', fn($q) => $q->where('status', '8'))
            ->get()
            ->groupBy('supplier_id')
            ->map(function ($items, $supplierId) {
                $total_qty = $items->sum('quantity');
                $amount    = $items->sum(fn($i) => $i->quantity * $i->product->variation->avg_price);

                return [
                    'supplier_id'      => (int) $supplierId,
                    'sold_out_qty'     => (int) $total_qty,
                    'sold_out_amount'  => (float) $amount,
                ];
            });

        // ---
        // 📈 Final Calculation (Including PO Financials)
        $supplierFinal = $supplierReceived->map(function ($item) use ($supplierSold, $inprocess, $poFinancials) {
            $supplierId = $item['supplier_id'];

            // --- Inventory Calculations (from previous steps) ---
            $soldItem      = $supplierSold->get($supplierId);
            $inprocessItem = $inprocess->get($supplierId);

            $sold_qty          = $soldItem['sold_out_qty'] ?? 0;
            $sold_amount       = $soldItem['sold_out_amount'] ?? 0;

            $inprocess_qty     = $inprocessItem['inprocess_qty'] ?? 0;
            $inprocess_amount  = $inprocessItem['inprocess_amount'] ?? 0;

            $balance_qty    = $item['stock_in_qty'] - $sold_qty - $inprocess_qty;
            $balance_amount = $balance_qty * $item['stock_in_price'];


            // --- New PO Financial Calculations ---
            $poItem = $poFinancials->get($supplierId);

            // Get aggregated amounts (default to 0 if no matching PO data is found)
            $total_order_amount    = (float) optional($poItem)->total_order_amount ?? 0;
            $total_remaining_amount = (float) optional($poItem)->total_remaining_amount ?? 0;

            // Calculate total paid amount
            $total_paid_amount = $total_order_amount - $total_remaining_amount;
            $balance = $sold_amount - $total_paid_amount;


            // --- Return Final Array ---
            return [
                'id'               => $supplierId,
                'name'             => $item['name'],
                'email'            => $item['email'],
                'contact'          => $item['contact'],

                // Inventory metrics
                'stock_in_qty'     => $item['stock_in_qty'],
                'stock_in_price'   => $item['stock_in_price'],
                'stock_in_amount'  => round($item['stock_in_amount']),
                'inprocess_qty'    => (int) $inprocess_qty,
                'inprocess_amount' => round((float) $inprocess_amount),
                'sold_out_qty'     => (int) $sold_qty,
                'sold_out_amount'  => round((float) $sold_amount),
                'balance_qty'      => (int) $balance_qty,
                'balance_amount'   => round((float) $balance_amount),

                // PO Financial metrics
                'po_total_order_amount'    => (float) $sold_amount,
                'po_total_paid_amount'     => (float) $total_paid_amount,
                'po_total_remaining_amount' => (float) $balance,

            ];
        })->values();

        $data = [
            'stock'           => $supplierReceivedValue,
            'total_payable'   => $totalPayable,
            'total_paid'      => $totalPaid,
            'total_remaining' => $totalRemaining,
            'remaining_dropshippers' => $remainingDropshippers,
            'suppliers' => $supplierFinal,
            'soldOut'   => $supplierIssuedValue
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function pendingYourmartPayment()
    {
        // Group purchase orders by supplier_id where supplier_stock = 0 and status = 1
        $suppliers = PurchaseOrder::select(
            'supplier_id',
            DB::raw('SUM(total_amount) as total_order_amount'),
            DB::raw('SUM(remaining_amount) as total_remaining_amount')
        )
            ->where('status', '1')
            ->where('supplier_stock', '0')
            ->groupBy('supplier_id')
            ->having('total_remaining_amount', '>', 0)
            ->with('supplier:id,full_name,email') // eager load supplier if needed
            ->orderByDesc('supplier_id')
            ->get();

        $totalPayable = $suppliers->sum('total_order_amount');
        $totalRemaining = $suppliers->sum('total_remaining_amount');
        $totalPaid = $totalPayable - $totalRemaining;
        $remainingDropshippers = $suppliers->count();

        $data = [
            'total_payable' => $totalPayable,
            'total_paid' => $totalPaid,
            'total_remaining' => $totalRemaining,
            'remaining_dropshippers' => $remainingDropshippers,
            'suppliers' => $suppliers
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function overallPayments(Request $request)
    {
        // Group purchase orders by supplier_id where supplier_stock = 0 and status = 1
        $suppliers = PurchaseOrder::select(
            'supplier_id',
            DB::raw('SUM(total_amount) as total_order_amount'),
            DB::raw('SUM(remaining_amount) as total_remaining_amount')
        )
            ->where('status', '1')
            ->when(isset($request->type) && $request->type !== '', function ($query) use ($request) {
                return $query->where('supplier_stock', $request->type);
            })
            ->groupBy('supplier_id')
            ->with('supplier:id,full_name,email') // eager load supplier if needed
            ->orderByDesc('supplier_id')
            ->get();

        return (new ResponseCollection($suppliers))
            ->response()
            ->setStatusCode(200);
    }

    public function paymentData(Request $request)
    {
        $banks = Bank::join('account_heads', 'banks.account_head_id', 'account_heads.id')
            ->select('account_heads.*', 'account_heads.id as code', 'account_heads.name as label')
            ->get();

        $cash = Cash::join('account_heads', 'cash.account_head_id', 'account_heads.id')
            ->select('account_heads.*', 'account_heads.id as code', 'account_heads.name as label')
            ->get();

        $supplier = Supplier::with('bank')->where('id', $request->id)->first();

        $orders = PurchaseOrder::where('status', '1')->where('supplier_stock', $request->type)->where('supplier_id', $supplier->id)->get();

        $supplier->total_profit = $orders->sum('total_amount');
        $supplier->total_paid_profit = $orders->sum('total_amount') - $orders->sum('remaining_amount');

        $data = [
            'orders'  => $orders,
            'banks'   => $banks,
            'cash'    => $cash,
            'supplier' =>  $supplier,
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function addPayment(Request $request)
    {
        $request->validate([
            'type'         => ['required'],
            'from_account' => ['required'],
            'amount'       => ['required'],
        ]);

        $supplier = Supplier::where('id', $request->id)->first();

       $orders = PurchaseOrder::where('supplier_id', $supplier->id)
            ->where('remaining_amount', '>', '0')
            ->where('status', '1')
            ->where('supplier_stock', $request->inventoryType)
            ->get();

        $ledger = new AccountHeadHelper();

        // Initialize remaining amount to the requested amount
        $remainingAmount = $request->amount;
        $document = $ledger->voucherType($request->type);

        $attachment = $request->attachment ? $this->attachment($request->attachment) : null;

        foreach ($orders as $order) {

            // Checks account if or not they are open
            $supplierLedger = $ledger->accountHeadCreate(
                $supplier->full_name . '-' . $supplier->cnic_number,
                2, // LIABILITIES
                8, // CURRENT LIABILITIES
                40, // TRADE CREDITORS
                41, // AP-SUPPLIERS
            );

            // Calculate the amount to pay for this order
            $orderProfit = $order->remaining_amount;

            if ($remainingAmount <= 0) {
                // If no remaining amount, exit the loop
                break;
            }

            // Determine the amount to pay for this order
            $amountToPay = min($orderProfit, $remainingAmount);

            // Only proceed if there is an amount to pay
            if ($amountToPay > 0) {

                // Shop Debit
                $ledger->accountTransaction($supplierLedger->id, $request->from_account, $amountToPay, 0, $request->narration, $document, $request->type == 'cash' ? 'CP' : 'BP', 'PO', $order->id, $approved = 1, $attachment);

                $order->decrement('remaining_amount', $amountToPay);

                // Reduce the remaining amount
                $remainingAmount -= $amountToPay;
            }
        }

        // Bank Cash Credit
        $ledger->accountTransaction($request->from_account, $supplierLedger->id, 0, $request->amount, $request->narration, $document, $request->type == 'cash' ? 'CP' : 'BP', 'PO', '0', $approved = 1, $attachment);
        $type = $request->type == 'cash' ? 'CP' : 'BP';

        NotificationHelper::addNotification(
            $title = 'Payout Sent',
            $messge = "Your payout Receipt # $type-$document has been sent successfully.",
            $link = null,
            $image = null,
            $directImage = null,
            $color    = 'green',
            $isPublic = 1,
            $user = $supplier->user_id
        );

        return response()->json([], 200);
    }

    public function paymentHistory(Request $request)
    {
        $supplier = Supplier::where('id', $request->id)->first();

        $name = $supplier->full_name . '-' . $supplier->cnic_number;
        $ledger = AccountHead::where('name', $name)->first();

        $transactions = AccountTransaction::with('po')
            ->where('posting_type', 'PO')
            ->where('account_head_id', $ledger->id)
            ->where(function ($query) {
                $query->where('type', 'BP')
                    ->orWhere('type', 'CP');
            })
            ->get();

        return (new ResponseCollection($transactions))
            ->response()
            ->setStatusCode(200);
    }

    public function attachment($image)
    {
        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '', $filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path            = $image->storeAs('public/uploads/dropshipper/payments/', $nameToStore);
        return $nameToStore;
    }

    public function pdf(Request $request)
    {
        // Fetch the DropShipper details with related bank and city data
        $details = Supplier::with('bank', 'city', 'shops')->findOrFail($request->id);

        // Create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Company Name');
        $pdf->SetTitle('Supplier Form');
        $pdf->SetSubject('Supplier Details');

        // Set header and footer data
        $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
        $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins and auto page breaks
        $pdf->SetMargins(10, 40, 10);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set font for the document
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 10, '', true);

        // Add a page
        $pdf->AddPage();

        // Title and subtitle
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Supplier Details', 0, 1, 'C');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Ln(5);

        // Create HTML content for the details
        $html = '
        <h4>Personal Information</h4>
        <table cellpadding="5" cellspacing="0" border="1">
            <tr>
                <td><strong>Full Name</strong></td>
                <td>' . $details->full_name . '</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>' . $details->email . '</td>
            </tr>
            <tr>
                <td><strong>CNIC Number</strong></td>
                <td>' . $details->cnic_number . '</td>
            </tr>
            <tr>
                <td><strong>WhatsApp Number</strong></td>
                <td>' . $details->whatsapp_number . '</td>
            </tr>
            <tr>
                <td><strong>Address</strong></td>
                <td>' . $details->address . '</td>
            </tr>
            <tr>
                <td><strong>City</strong></td>
                <td>' . ($details->city ? $details->city->name : 'N/A') . '</td>
            </tr>
        </table>

        <br><h4>Bank Information</h4>
        <table cellpadding="5" cellspacing="0" border="1">
            <tr>
                <td><strong>Bank Name</strong></td>
                <td>' . ($details->bank ? $details->bank->name : 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>Account Number</strong></td>
                <td>' . $details->account_number . '</td>
            </tr>
            <tr>
                <td><strong>Account Title</strong></td>
                <td>' . $details->account_title . '</td>
            </tr>
              <tr>
                <td><strong>Account Title</strong></td>
                <td>' . $details->account_iban . '</td>
            </tr>
            <tr>
                <td><strong>Account Title</strong></td>
                <td>' . $details->payment_cycle . '</td>
            </tr>
        </table>
        <br><h4>Business Information</h4>
        ';

        // Add shops information in a loop
        if (!empty($details->shops)) {
            foreach ($details->shops as $index => $shop) {
                $html .= '<br><h4>Shop ' . ($index + 1) . ' Details</h4>
        <table cellpadding="5" cellspacing="0" border="1">
            <tr>
                <td><strong>Store Name</strong></td>
                <td>' . ($shop->store_name ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>Store URL</strong></td>
                <td>' . ($shop->store_url ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>Social Media Profile</strong></td>
                <td>' . ($shop->social_media_profile_link ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>Business Description</strong></td>
                <td>' . ($shop->business_description ?? 'N/A') . '</td>
            </tr>
        </table>';
            }
        }

        // Output the HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Set PDF to display as inline in the browser
        $pdf->Output('supplier_form.pdf', 'I');
    }
}

class MYPDF extends TCPDF
{

    //Page header
    public function Header()
    {
        // Logo
        // $image_file = K_PATH_IMAGES . '';
        // $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // // Set font
        $this->SetFont('helvetica', 'B', 14);
        $this->Ln(5);
        // Title
        $this->Cell(0, 15, 'Supplier Form', 0, 1, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetFont('helvetica', '', 12);

        $this->Cell(0, 0, "", 'B', 1, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer()
    {
        $user_name = auth()->user()->name;
        $date_now = date('d-M-Y h:i A', strtotime(now()));
        $this->SetFont('times', '', 9);
        //   Position at 15 mm from bottom
        $this->Ln(-15);
        $this->SetFont('times', '', 8);
        $this->Cell(0, 0, '"Errors and omissions excepted" (E&OE)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetFont('times', 'B', 9);
        $this->Cell(0, 0, 'Printed By : ' . $user_name . ' || ' . $date_now, 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetFont('times', '', 8);
        $this->Cell(0, 0, 'Developed By SAR ZONE', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
    }
}
