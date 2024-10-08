<?php

namespace App\Http\Controllers\Inventory\Order;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\LeopardApiHelper;
use App\Http\Resources\ResponseCollection;
use App\Models\City;
use App\Models\Inventory\Courier\CourierCategoryRange;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderActivity;
use App\Models\Inventory\Order\OrderComment;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\Store\StoreIssuance;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReturn;
use App\Models\Inventory\Store\StoreReturnDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OrderController extends Controller
{
    public function index()
    {
        return view('inventory.product.order.orders');
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


        if ($userRole == 'admin' || $userRole == 'supervisor') {
            $orders = Order::with('user', 'shop')
                ->orderBy('id', 'desc')
                // Apply status filter when provided
                ->when($request->status, function ($query, $status) {
                    return $query->where('status', $status);
                })
                // Apply date range filters when provided
                ->when($request->from, function ($query, $from) {
                    return $query->whereDate('created_at', '>=', $from);
                })
                ->when($request->to, function ($query, $to) {
                    return $query->whereDate('created_at', '<=', $to);
                })
                ->get();
        }else {
            $orders = Order::with('user', 'shop')->where('status', $statusMap[$userRole])
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
            'returns.details.variation.product',
            'returns.details.variation.images.attachment',
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
            'remaining_amount' => (float)$order->total_bill - (float)$request->amount,
        ]);

        return response()->json(['message' => 'Order status updated successfully.'], 200);
    }

    public function updateStatus(Request $request)
    {

        $userRole  = trim(auth()->user()->role);

        $order = Order::with('items')->find($request->id);

        // Map roles to corresponding statuses
        $statusMap = [
            'order collection manager' => 0,    // Role for order collection
            'inventory manager' => 1,  // Role for inventory issuance
            'qc manager' => 2,         // Role for quality control
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

            if ($userRole == 'order collection manager') {

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
                    'slip_link'             => $leopardData['slip_link']
                ]);
            }

            if ($userRole == 'inventory manager') {
                $issuance = StoreIssuance::create([
                    'order_id'  => $request->id,
                    'added_by'  => auth()->user()->id,
                ]);
                foreach ($order->items as $product) {
                    $variation = ProductVariation::where('id', $product->product_variation_id)->first();
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
        else if ($userRole == 'admin') {

            if ($order->status == '0') {

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
                    'slip_link'             => $leopardData['slip_link']
                ]);
            }

            if ($order->status == '1') {
                $issuance = StoreIssuance::create([
                    'order_id'  => $request->id,
                    'added_by'  => auth()->user()->id,
                ]);
                foreach ($order->items as $product) {
                    $variation = ProductVariation::where('id', $product->product_variation_id)->first();
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
        $order->increment('status');
        return response()->json(['message' => 'Order status updated successfully.'], 200);
    }

    public function reject(Request $request)
    {

        $order = Order::with('items')->find($request->id);
        if (auth()->user()->role == 'admin') {

            OrderActivity::create([
                'order_id'  => $request->id,
                'activity'  => 'Order rejected',
                'added_by'  => auth()->user()->id,
            ]);

            if ($order->status > 1) {
                $srn = StoreReturn::create([
                    'order_id'        => $order->id,
                    'dropshipper_id'  => $order->belongs_to,
                    'remarks'         => 'Rejected',
                    'added_by'        => auth()->user()->id
                ]);

                foreach ($order->items as $product) {
                    StoreReturnDetail::create([
                        'srn_id'     => $srn->id,
                        'product_id' => $product->product_variation_id,
                        'quantity'   => $product->quantity,
                        'price'      => $product->price,
                        'total'      => (float)$product->quantity * (float)$product->price,
                        'added_by'   => auth()->user()->id
                    ]);

                    ProductVariation::where('id', $product->product_variation_id)
                        ->increment('stock', $product->quantity);
                }
            }

            $order->update(['status' => '7']);

            $response = Http::post('https://merchantapi.leopardscourier.com/api/cancelBookedPackets/format/json/', [
                'api_key' => '487F7B22F68312D2C1BBC93B1AEA445B1726751602',
                'api_password' => 'Allah@001#',
                'cn_numbers' => $order->tracking_number, // or 'XXYYYYYYYY,XXYYYYYYYY,XXYYYYYY'
            ]);
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

        if ( $order->status > 1) {
            $srn = StoreReturn::create([
                'order_id'        => $order->id,
                'dropshipper_id'  => $order->belongs_to,
                'remarks'         => 'Rejected',
                'added_by'        => auth()->user()->id
            ]);

            foreach ($order->items as $product) {
                StoreReturnDetail::create([
                    'srn_id'     => $srn->id,
                    'product_id' => $product->product_variation_id,
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
