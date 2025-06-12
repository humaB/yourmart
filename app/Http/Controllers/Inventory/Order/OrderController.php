<?php

namespace App\Http\Controllers\Inventory\Order;

use App\Http\Controllers\Account\Helper\AccountHeadHelper;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\LeopardApiHelper;
use App\Http\Controllers\Helpers\PostExApiHelper;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Account\AccountTransaction;
use App\Models\City;
use App\Models\Inventory\Courier\CourierCategoryRange;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderActivity;
use App\Models\Inventory\Order\OrderComment;
use App\Models\Inventory\Order\OrderDispatchedRecord;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Order\OrderLeopardStatus;
use App\Models\Inventory\Order\OrderReAttempt;
use App\Models\Inventory\Product\Setting\OtherCharge;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\Store\StoreIssuance;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReturn;
use App\Models\Inventory\Store\StoreReturnDetail;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    private $apiKey = '487F7B22F68312D2C1BBC93B1AEA445B1726751602';
    private $apiPassword = 'Allah@001#';

    public function index()
    {
        return view('inventory.product.order.orders');
    }

    public function record()
    {
        return view('inventory.product.order.order_record');
    }

    public function dispatchIndex()
    {
        return view('inventory.product.order.order_dispatch');
    }

    public function  postExAirbill(Request $request)
    {

        $response = Http::withHeaders([
            'token' => 'ZWExMGNhYWFkYjM3NGM3MzhkZWZkN2M0M2M5YjhhZjU6MTY2ZjRiMmQ1YWVmNDkyOTg5OTE5NmUwMTkzNjdiYjg=',
        ])->get('https://api.postex.pk/services/partnerintegration/api/order/airway-bill', [
            'trackingNumbers' => $request->tracking,
        ]);

        if ($response->successful()) {
            return response($response->body(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="airway-bill.pdf"');
        } else {
            return response()->json([
                'error' => 'Failed to fetch airway bill',
                'status' => $response->status(),
                'message' => $response->body(),
            ], $response->status());
        }
    }

    public function fetchOrders(Request $request)
    {
        $userRole  = trim(auth()->user()->role);
        // Map roles to corresponding statuses
        $statusMap = [
            'order collection manager' => 0,    // Role for order collection
            'inventory manager' => 1,  // Role for inventory issuance
            'qc manager' => 2,         // Role for quality control
            'packing & dispatch manager' => 3,    // Role for packing and dispatch
            'auditor'                   => 4    // Autidor
        ];

        $status = $request->status;

        if ($userRole == 'admin' || $userRole == 'supervisor') {
            $dropshipper = null;

            if ($request->droshipper != 0) {
                $dropshipper = $request->droshipper;
                $dropshipper = DropShipper::where('id', $dropshipper)->first();
                $dropshipper = $dropshipper->user_id ?? 0;
            }

            $orders = Order::with('user', 'shop', 're_attempt')
                ->orderBy('id', 'desc')
                // Apply status filter when provided
                ->when($status != '', function ($query) use ($status) {
                    return $query->where('status', "$status");
                })
                // Apply date range filters when provided
                ->when($request->from, function ($query, $from) {
                    return $query->whereDate('created_at', '>=', $from);
                })
                ->when($request->to, function ($query, $to) {
                    return $query->whereDate('created_at', '<=', $to);
                })
                ->when($request->type, function ($query, $type) {
                    return $query->where('type', $type);
                })
                ->when($dropshipper, function ($query, $dropshipper) {
                    return $query->where('belongs_to', $dropshipper);
                })
                ->when($request->courier, function ($query, $courier) {
                    return $query->where('courier_service_id', $courier);
                })
                ->get();
        } else {
            $orders = Order::with('user', 'shop', 're_attempt')->where('status', $statusMap[$userRole])
                ->orderBy('id', 'desc')
                ->get();
        }

        $data = [
            'orders' => $orders,
            'role'   =>  $userRole
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function fetchOrderRecord()
    {

        $orders = Order::with('user', 'shop')
            ->orderBy('id', 'desc')
            ->get();

        return (new ResponseCollection($orders))
            ->response()
            ->setStatusCode(200);
    }


    public function trackingDetails(Request $request)
    {
        $orders = OrderLeopardStatus::where('order_id', $request->id)->orderBy('updated_at', 'desc')->get();

        return (new ResponseCollection($orders))
            ->response()
            ->setStatusCode(200);
    }

    public function reAttempt(Request $request)
    {

        $order = Order::where('id', $request->id)->first();
        $check = OrderReAttempt::where('order_id', $order->id)->first();
        if ($check) {
            return (new ValidationCollection(['Re-delivery attempt already in progress for this order']))
                ->response()
                ->setStatusCode(421);
        }

        if ($order->courier_service_id == '1') {
            $response = Http::post('https://merchantapi.leopardscourier.com/api/shipperAdviceList/format/json/', [
                'api_key' => $this->apiKey,
                'api_password' => $this->apiPassword,
                'product' => '',
                'status' => '',
                'origionID' => '',
                'destinationID' => '',
                'dateFrom' => '',
                'toDate' => '',
                'Cn_number' =>  "",
                'start' => 0,
                'length' => 100
            ]);

            $trackingId = "";
            // Check if the request was successful
            if ($response->successful()) {
                // Process the response
                $responseData = $response->json();
                if (isset($responseData['data']) && count($responseData['data']) > 0) {
                    foreach ($responseData['data'] as $item) {
                        if ($item['cn_number'] == $order->tracking_number) {
                            $trackingId = $item['id'];
                        }
                    }
                }
            }

            if (!$trackingId) {
                return (new ValidationCollection(['Re-delivery attempt already in progress for this order']))
                    ->response()
                    ->setStatusCode(421);
            }

            $response = Http::post('https://merchantapi.leopardscourier.com/api/updateShipperAdvice/format/json/', [
                'api_key' => $this->apiKey,
                'api_password' => $this->apiPassword,
                'data' => [
                    [
                        'id'        => $trackingId,
                        'cn_number' =>  $order->tracking_number ?? "",
                        'shipper_advice_status' => 'RA', // allowed statuses are ('RA', 'RT')
                        'shipper_remarks' => $request->remarks
                    ],
                    // Add more data here...
                ]
            ]);
        }
        if ($order->courier_service_id == '2') {
            $postEx = new PostExApiHelper();
            $response = $postEx->reAttempt($order->tracking_number);
            if ($response->successful()) {
                $response->json(); // or handle successful response
            } else {
                return (new ValidationCollection(["Can't re-attempt on this order"]))
                    ->response()
                    ->setStatusCode(421);
            }
        }

        OrderReAttempt::create([
            'order_id' => $order->id,
            'advice'   => $request->remarks
        ]);

        OrderActivity::create([
            'order_id'  => $order->id,
            'activity'  => 'Re-attempt try added with remarks : ' . $request->remarks,
            'added_by'  => auth('sanctum')->user()->id
        ]);

        return response()->json([], 200);
    }

    public function pendingDispatchs(Request $request)
    {

        $dispatched = OrderDispatchedRecord::with('order.shop', 'tracking', 'order.courier')
            ->when($request->from, function ($query, $from) {
                return $query->whereDate('created_at', '>=', $from);
            })
            ->when($request->to, function ($query, $to) {
                return $query->whereDate('created_at', '<=', $to);
            })
            ->when($request->courier, function ($query, $courier) {
                return $query->whereHas('order', function ($q) use ($courier) {
                    $q->where('courier_service_id', $courier);
                });
            })
            ->orderBy('id', 'desc')
            ->get();

        $orders = Order::with('shop', 'user', 'courier')
            ->where('type', 'Normal')
            ->whereIn('status', ['4', '5'])
            ->when($request->from, function ($query, $from) {
                return $query->whereDate('created_at', '>=', $from);
            })
            ->when($request->to, function ($query, $to) {
                return $query->whereDate('created_at', '<=', $to);
            })
            ->when($request->courier, function ($query, $courier) {
                return $query->where('courier_service_id', $courier);
            })
            ->whereNotIn('id', $dispatched->pluck('order_id'))
            ->orderBy('id', 'desc')->get();

        $data = [
            'pendings'   => $orders,
            'dispatched' => $dispatched
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function addDispatched(Request $request)
    {

        OrderDispatchedRecord::create([
            'order_id'        => $request->id,
            'tracking_number' => $request->tracking_number,
            'total_amount'    => $request->total_bill,
            'added_by'        => auth()->user()->id
        ]);

        return ['message' => 'Order Dispatched'];
    }

    public function markasReplacement(Request $request)
    {

        Order::where('id', $request->id)->update([
            'is_replacement' => '1'
        ]);

        return ['message' => 'Marked as Replacement'];
    }

    public function markasBeingReturn(Request $request)
    {
        $order = Order::where('id', $request->id)->first();
        $totalReceivable = $order->courier_service_price + $order->packaging_price;
        $totalReceived = $order->paid_amount;
        $difference = $totalReceivable - $totalReceived;
        if ($totalReceivable > $totalReceived) {
            $totalPayable = -$difference;
        } else {
            $totalPayable = -$difference;
        }

        Order::where('id', $request->id)->update([
            'status' => '9',
            'total_profit' => $totalPayable
        ]);

        if ($difference != 0) {
            $ledger = new AccountHeadHelper();
            $document = $ledger->voucherType('JV');

            $shop = DropShipperShop::where('id', $order->shop_id)->first();
            $head_id = $shop->account_head_id;
            $courierCharges = $order->courier_service_price;
            $packingCharges = $order->packaging_price;
            $otherCharges = OtherCharge::where('type', 'Return')->where('status', '0')->first();
            $otherCharges = (float)$otherCharges->amount;
            $courierExtraCharges = $order->range->our_charges;

            $dropshipper = DropShipper::where('user_id', $order->belongs_to)->first();
            //60 are extra charges which will in future be set by admin
            $dropshipper->decrement('total_payable', $courierCharges + $packingCharges +  $otherCharges);
            $dropshipper->decrement('remaining_amount', $courierCharges + $packingCharges +  $otherCharges);

            $shop->decrement('total_payable', $courierCharges + $packingCharges +  $otherCharges);
            $shop->decrement('total_remaining', $courierCharges + $packingCharges +  $otherCharges);

            $document = $ledger->voucherType('JV');
            $ledger->accountTransaction($head_id, 74, $courierCharges + $packingCharges + $otherCharges, 0, 'Total Receivable Amount', $document, 'JV', 'order', $order->id, $approved = 1);
            //Leopard Credit
            $ledger->accountTransaction(73, $head_id, 0, $courierCharges -  $courierExtraCharges, 'Courier Charges', $document, 'JV', 'order', $order->id, $approved = 1);
            //Sale Credit
            $ledger->accountTransaction(74, $head_id, 0, $packingCharges + $otherCharges + $courierExtraCharges, 'Packaging Charges', $document, 'JV', 'order', $order->id, $approved = 1);

            //Meezan Bank Debit
            if ($order->paid_amount > 0) {
                $document = $ledger->voucherType('bank');
                //Bank Cash Debit
                $ledger->accountTransaction(75, $head_id, $order->paid_amount, 0, 'Advance Payment received against order', $document, 'BR', 'order', $order->id, $approved = 1);
                //Dropshipper Credit
                $ledger->accountTransaction($head_id, 75, 0, $order->paid_amount, 'Advance Payment against order', $document, 'BR', 'order', $order->id, $approved = 1);

                $dropshipper->increment('total_payable', $order->paid_amount);
                $dropshipper->increment('remaining_amount', $order->paid_amount);

                $shop->increment('total_payable', $order->paid_amount);
                $shop->increment('total_remaining', $order->paid_amount);
            }
        }

        // Create activity log
        OrderActivity::create([
            'order_id'  => $order->id,
            'activity'  => 'Order marked as being return',
            'added_by'  => auth()->user()->id,
        ]);

        return ['message' => 'Marked as Being Return'];
    }

    public function markasDelivered(Request $request)
    {

        $order = Order::where('id', $request->id)->first();
        if ($order->status != '8') {
            $leopard = new LeopardApiHelper();

            $leopard->parcelDelivered($order);

            Order::where('id', $request->id)->update([
                'status' => '8'
            ]);

            // Create activity log
            OrderActivity::create([
                'order_id'  => $order->id,
                'activity'  => 'Order marked as delivered',
                'added_by'  => auth()->user()->id,
            ]);
        }

        return ['message' => 'Marked as Delivered'];
    }

    public function markasNotDelivered(Request $request)
    {

        $order = Order::where('id', $request->id)->first();
        if ($order->status == '8') {
            $leopard = new LeopardApiHelper();

            $leopard->reverseAccountOnDelivered($order);

            Order::where('id', $request->id)->update([
                'status' => '11'
            ]);

            // Create activity log
            OrderActivity::create([
                'order_id'  => $order->id,
                'activity'  => 'Order marked as not delivered',
                'added_by'  => auth()->user()->id,
            ]);
        }

        return ['message' => 'Marked as Not Delivered'];
    }

    public function multipleActions(Request $request)
    {
        if ($request->action == 'Product List') {
            $data = OrderItem::with('variation.product', 'variation.images.attachment')
                ->whereIn('order_id', $request->products)
                ->get()
                ->groupBy('product_variation_id')
                ->map(function ($items) {
                    return [
                        'sku'      => $items->first()->variation->sku,
                        'product'  => $items->first()->variation->product->title,
                        'quantity' => $items->sum('quantity'),
                        'image'    => optional($items->first()->variation->images->first())->attachment->attachment,
                    ];
                })
                ->values()
                ->toArray();
        } else {
            $data = Order::whereIn('id', $request->products)->get();
        }

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function details(Request $request)
    {

        $orders = Order::with(
            'city',
            'shop',
            'user.dropshipper',
            'courier',
            'activity.user:id,name',
            'range.category',
            'items.variation.product',
            'comments.user',
            'items.variation.images.attachment',
            'items.variation.barcode',
            'items.variation.color',
            'items.variation.size',
            'returns.details.product',
            'returns.details.product.variation.images.attachment',
            'attachments',
            're_attempt',

            //For Daraz Order
            'daraz_labels'
        )->where('id', $request->id)->get();

        return (new ResponseCollection($orders))
            ->response()
            ->setStatusCode(200);
    }

    public function updatePaidAmount(Request $request)
    {
        $order = Order::find($request->id);

        $order->update([
            'paid_amount'    => $request->amount,
            'advance_amount' => $request->amount,
            'remaining_amount' => (float)$order->total_bill - (float)$request->amount,
        ]);

        return response()->json(['message' => 'Order status updated successfully.'], 200);
    }

    public function updatePackagingAmount(Request $request)
    {
        $order = Order::find($request->id);

        $previousTotal = (float)$order->total_bill - (float)$order->packaging_price;

        $order->update([
            'packaging_price'  => $request->amount,
            'total_bill'       => (float)$previousTotal + (float)$request->amount,
            'remaining_amount' => ((float)$previousTotal + (float)$request->amount) - $order->paid_amount,
        ]);

        $items = OrderItem::where('order_id', $order->id)->get();
        foreach ($items as $item) {
            $quantity = $item->quantity;
            $singlePrice = $item->price;

            $totalItemPrice = $singlePrice * $quantity;
            //Calculate Packaging
            $extraPackagingCharges = ((float)$request->amount / (float)$previousTotal) * (float)$totalItemPrice;

            $item->update([
                'packaging_cost'  => round($extraPackagingCharges),
                'sell_price'     => round($totalItemPrice + $extraPackagingCharges),
            ]);
        }

        return response()->json(['message' => 'Order packaging amount updated successfully.'], 200);
    }

    public function addDiscount(Request $request)
    {
        $order = Order::find($request->id);

        //Calculate Discount
        $subTotal = $order->total_bill - ((float)$order->packaging_price + (float)$order->courier_service_price);
        $afterDiscountAmount = $subTotal - $request->amount;
        $newTotal = $afterDiscountAmount + ((float)$order->packaging_price + (float)$order->courier_service_price);
        $paidAmount = $order->total_bill - $order->remaining_amount;

        $order->update([
            'total_bill'       => $newTotal,
            'remaining_amount' => $newTotal - $paidAmount,
            'discount'         => $request->amount
        ]);

        $totalCourierAmount = $order->courier_service_price;
        $totalPackagePrice  = $order->packaging_price;

        $items = OrderItem::where('order_id', $order->id)->get();
        $totalSellPrice  = $items->sum('sell_price');
        foreach ($items as $item) {

            $quantity = $item->quantity;
            $singlePrice = $item->price;

            $totalItemPrice = $singlePrice * $quantity;

            $discount =  ((float)$request->amount / (float)$subTotal) * (float)$totalItemPrice;
            //Calculate Courier
            $extraCourierCharges = ((float)$totalCourierAmount / (float)$subTotal) * (float)$totalItemPrice;
            //Calculate Packaging
            $extraPackagingCharges = ((float)$totalPackagePrice / (float)$subTotal) * (float)$totalItemPrice;
            //Calculate Selling Price
            $sellPrice = ((float)$totalSellPrice / (float)$subTotal) * (float)$totalItemPrice;

            $buyPrice = $singlePrice - ($discount / $quantity);

            // Update the order item in the database
            $item->update([
                'price'          => round($buyPrice),
                'sell_price'     => round($sellPrice),
                'courier_cost'   => round($extraCourierCharges),
                'packaging_cost' => round($extraPackagingCharges),
                'discount'       => round($discount / $quantity),
            ]);
        }

        return response()->json(['message' => 'Order packaging amount updated successfully.'], 200);
    }

    public function updateStatus(Request $request)
    {
        $userRole  = trim(auth()->user()->role);

        $order = Order::with('items')->find($request->id);

        // Map roles to corresponding statuses
        $statusMap = [
            'order collection manager'   => 0,    // Role for order collection
            'inventory manager'          => 1,  // Role for inventory issuance
            'qc manager'                 => 2,         // Role for quality control
            'packing & dispatch manager' => 3,    // Role for packing and dispatch
            'auditor' => 4   // Role for audit
        ];

        // Get current order status
        $currentStatus = $order->status;

        // Check if user is admin
        if (auth()->user()->role === 'admin') {
            // Allow admin to forward to any role
            $nextStatus = $currentStatus + 1;
            if ($nextStatus > max($statusMap)) {
                $nextStatus = max($statusMap); // Prevent exceeding max status
            }
        } else {
            // Get user's role
            $userRole = auth()->user()->role;
            // Find next status based on user's role

            $nextStatus = array_search($userRole, array_keys($statusMap)) + 1;
            $nextStatus = $statusMap[array_search($nextStatus, $statusMap)] ?? $currentStatus;
        }

        // Get next role based on next status
        $nextRole = array_search($nextStatus, $statusMap);

        if ($nextRole) {
            // Create activity log
            OrderActivity::create([
                'order_id'  => $request->id,
                'activity'  => 'Order sent to ' . $nextRole,
                'added_by'  => auth()->user()->id,
            ]);
        }

        if (array_key_exists($userRole, $statusMap)) {

            if ($userRole == 'order collection manager' && $order->type == 'Normal') {

                if ($order->courier_service_id == '1') {
                    $leopardData = [
                        'track_number' => null,
                        'slip_link'    => null
                    ];

                    $leopardApi = new LeopardApiHelper();
                    $city = City::where('id', $order->city_id)->first();
                    $range = CourierCategoryRange::where('id', $order->range_id)->first();
                    $leopardData = $leopardApi->bookAPacket($order->total_weight, $order, $order->order_no, $order->shop_id, $city, $range->category_id);

                    $order->update([
                        'tracking_number'       => $leopardData['track_number'],
                        'slip_link'             => $leopardData['slip_link'],
                    ]);
                }

                if ($order->courier_service_id == '2') {
                    $postExApi = new PostExApiHelper();
                    $postExData = $postExApi->bookAPacket($order, $order->order_no, $order->shop_id);

                    if ($postExData['error'] && $postExData['error'] != '') {
                        return (new ValidationCollection([$postExData['error']]))
                            ->response()
                            ->setStatusCode(421);
                    }

                    $order->update([
                        'tracking_number'       => $postExData['track_number'],
                    ]);
                }
            }

            if ($userRole == 'inventory manager') {
                $issuance = StoreIssuance::create([
                    'order_id'  => $request->id,
                    'added_by'  => auth()->user()->id,
                ]);
                foreach ($order->items as $product) {
                    $variation = ProductVariation::where('id', $product->product_variation_id)->first();
                    if ($variation) {
                        $variation->decrement('stock', $product->quantity);

                        StoreIssuanceDetail::create([
                            'sin_id'     => $issuance->id,
                            'product_id' => $variation->product_id,
                            'quantity'   => $product->quantity,
                            'price'      => $product->price,
                            'total'      => (float)$product->quantity * (float)$product->price,
                            'added_by'  => auth()->user()->id,
                        ]);
                    }
                }
            }
        } else if ($userRole == 'admin') {

            if ($order->status == '0' && $order->type == 'Normal') {

                if ($order->courier_service_id == '1') {
                    $leopardData = [
                        'track_number' => null,
                        'slip_link'    => null
                    ];

                    $leopardApi = new LeopardApiHelper();
                    $city = City::where('id', $order->city_id)->first();
                    $range = CourierCategoryRange::where('id', $order->range_id)->first();
                    $leopardData = $leopardApi->bookAPacket($order->total_weight, $order, $order->order_no, $order->shop_id, $city, $range->category_id);

                    if ($leopardData['error'] && $leopardData['error'] != '') {
                        return (new ValidationCollection([$leopardData['error']]))
                            ->response()
                            ->setStatusCode(421);
                    }

                    $order->update([
                        'tracking_number'       => $leopardData['track_number'],
                        'slip_link'             => $leopardData['slip_link'],
                    ]);
                }

                if ($order->courier_service_id == '2') {
                    $postExApi = new PostExApiHelper();
                    $postExData = $postExApi->bookAPacket($order, $order->order_no, $order->shop_id);

                    if ($postExData['error'] && $postExData['error'] != '') {
                        return (new ValidationCollection([$postExData['error']]))
                            ->response()
                            ->setStatusCode(421);
                    }

                    $order->update([
                        'tracking_number'       => $postExData['track_number'],
                    ]);
                }
            }

            if ($order->status == '1') {
                $issuance = StoreIssuance::create([
                    'order_id'  => $request->id,
                    'added_by'  => auth()->user()->id,
                ]);
                foreach ($order->items as $product) {
                    $variation = ProductVariation::where('id', $product->product_variation_id)->first();
                    if ($variation) {
                        $variation->decrement('stock', $product->quantity);

                        StoreIssuanceDetail::create([
                            'sin_id'     => $issuance->id,
                            'product_id' => $variation->product_id,
                            'quantity'   => $product->quantity,
                            'price'      => $product->price,
                            'total'      => (float)$product->quantity * (float)$product->price,
                            'added_by'  => auth()->user()->id,
                        ]);
                    }
                }
            }
        }
        if ($order->type != 'Cash') {
            $order->increment('status');
        } else {
            $order->update([
                'status' => '5'
            ]);
        }
        return response()->json(['message' => 'Order status updated successfully.'], 200);
    }

    public function reject(Request $request)
    {

        $order = Order::with('items')->find($request->id);
        if (auth()->user()->role == 'admin') {

            if ($order->type == 'Cash') {
                $transactions = AccountTransaction::where('posting_type', 'order')
                    ->where(function ($q) {
                        $q->where('type', 'CR')
                            ->orWhere('type', 'BR');
                    })
                    ->where('posting_id', $order->id)
                    ->get()
                    ->map(function ($transaction) {
                        [$transaction->debit, $transaction->credit] = [$transaction->credit, $transaction->debit];
                        return $transaction;
                    });

                $ledger = new AccountHeadHelper();
                //Return Bank or Cash Entry
                $document = $ledger->voucherType('JV');
                foreach ($transactions as $transaction) {
                    $ledger->accountTransaction($transaction->account_head_id, $transaction->other_account_head_id, $transaction->debit, $transaction->credit, 'Order Reject and payment refund', $document, 'JV', 'order', $order->id, $approved = 1);
                }
            }

            OrderActivity::create([
                'order_id'  => $request->id,
                'activity'  => 'Order rejected',
                'added_by'  => auth()->user()->id,
            ]);

            $checkedIssuance = StoreIssuance::where('order_id', $order->id)->first();
            if ($order->status > 1 && $checkedIssuance) {
                $srn = StoreReturn::create([
                    'order_id'        => $order->id,
                    'dropshipper_id'  => $order->belongs_to,
                    'remarks'         => 'Rejected',
                    'added_by'        => auth()->user()->id
                ]);

                foreach ($order->items as $product) {
                    $variation = ProductVariation::where('id', $product->product_variation_id)->first();
                    StoreReturnDetail::create([
                        'srn_id'     => $srn->id,
                        'product_id' => $variation->product_id,
                        'quantity'   => $product->quantity,
                        'price'      => $product->price,
                        'total'      => (float)$product->quantity * (float)$product->price,
                        'added_by'   => auth()->user()->id
                    ]);

                    ProductVariation::where('id', $product->product_variation_id)
                        ->increment('stock', $product->quantity);
                }
            }

            // Leopard
            if ($order->courier_service_id == '1') {
                $response = Http::post('https://merchantapi.leopardscourier.com/api/cancelBookedPackets/format/json/', [
                    'api_key' => '487F7B22F68312D2C1BBC93B1AEA445B1726751602',
                    'api_password' => 'Allah@001#',
                    'cn_numbers'   => $order->tracking_number, // or 'XXYYYYYYYY,XXYYYYYYYY,XXYYYYYY'
                ]);
            }

            // PostEx
            if ($order->courier_service_id == '2') {
                $postEx = new PostExApiHelper();
                $postEx->cancelOrder($order->tracking_number);
            }

            $order->update(['status' => '7']);
        } else {
            $order->update(['status' => '6']);
            OrderActivity::create([
                'order_id'  => $request->id,
                'activity'  => 'Order sent for final approval from admin to reject',
                'added_by'  => auth()->user()->id,
            ]);
        }

        return response()->json(['message' => 'Order status updated successfully.'], 200);
    }

    public function revert(Request $request)
    {

        $order = Order::with('items')->find($request->id);

        if ($order->status > 1) {
            $srn = StoreReturn::create([
                'order_id'        => $order->id,
                'dropshipper_id'  => $order->belongs_to,
                'remarks'         => 'Rejected',
                'added_by'        => auth()->user()->id
            ]);

            foreach ($order->items as $product) {
                $variation = ProductVariation::where('id', $product->product_variation_id)->first();

                StoreReturnDetail::create([
                    'srn_id'     => $srn->id,
                    'product_id' => $variation->product_id,
                    'quantity'   => $product->quantity,
                    'price'      => $product->price,
                    'total'      => (float)$product->quantity * (float)$product->price,
                    'added_by'   => auth()->user()->id
                ]);

                ProductVariation::where('id', $product->product_variation_id)
                    ->increment('stock', $product->quantity);
            }
        }

        $order->decrement('status');

        // Map roles to corresponding statuses
        $statusMap = [
            0 => 'order collection manager',   // Role for order collection
            1 => 'inventory manager',          // Role for inventory issuance
            2 => 'qc manager',                 // Role for quality control
            3 => 'packing & dispatch manager', // Role for packing and dispatch
            4 => 'auditor'                     // Role for audit
        ];

        // Get the current status after decrement
        $currentStatus = $order->status;

        $role = $statusMap[$currentStatus]; // Get the role corresponding to the new status

        // Create activity log
        OrderActivity::create([
            'order_id'  => $order->id,
            'activity'  => 'Order reverted back to ' . $role,
            'added_by'  => auth()->user()->id,
        ]);

        return response()->json(['message' => 'Order status updated successfully.'], 200);
    }

    public function comment(Request $request)
    {
        if ($request->comment) {
            OrderComment::create([
                'order_id' => $request->id,
                'comment'           => $request->comment,
                'attachment'        => $request->attachment ? $this->commentAttachment($request->attachment) : '',
                'added_by'          => auth()->user()->id,
            ]);
        }

        return ['message' => 'Comment Added Successfully'];
    }

    public function deleteComment(Request $request)
    {
        if ($request->comment) {
            OrderComment::where('id', $request->comment)->delete();
        }

        return ['message' => 'Comment deleted Successfully'];
    }

    public function commentAttachment($image)
    {

        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '', $filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path            = $image->storeAs('public/uploads/order/comments/attachments', $nameToStore);
        return $nameToStore;
    }
}
