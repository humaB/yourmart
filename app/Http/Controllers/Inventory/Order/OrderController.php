<?php

namespace App\Http\Controllers\Inventory\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
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

class OrderController extends Controller
{
    public function index()
    {
        return view('inventory.product.order.orders');
    }

    public function fetchOrders(){
        $userRole  = trim(auth()->user()->role);
          // Map roles to corresponding statuses
        $statusMap = [
            'order collection manager' => 0,    // Role for order collection
            'inventory manager' => 1,  // Role for inventory issuance
            'qc manager' => 2,         // Role for quality control
            'packing & dispatch manager' => 3,    // Role for packing and dispatch
            'auditor'                   => 4    // Autidor
        ];


        if( $userRole != 'admin'){
            $orders = Order::with('user', 'shop')->where('status', $statusMap[$userRole])
            ->orderBy('id', 'desc')
            ->get();
        }else{
            $orders = Order::with('user', 'shop')
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
            'items.variation.size'
            )->where('id', $request->id)->get();

        return (new ResponseCollection($orders))
            ->response()
            ->setStatusCode(200);
    }

    public function updateStatus( Request $request ){

        $userRole  = trim(auth()->user()->role);

        $order = Order::with('items')->find($request->id);

        // Map roles to corresponding statuses
        $statusMap = [
            'order collection manager' => 1,    // Role for order collection
            'inventory manager' => 2,  // Role for inventory issuance
            'qc manager' => 3,         // Role for quality control
            'packing & dispatch manager' => 4,    // Role for packing and dispatch
            'auditor' => 5    // Role for audit
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

        if( $nextRole ){
            // Create activity log
            OrderActivity::create([
                'order_id'  => $request->id,
                'activity'  => 'Order sent to '.$nextRole,
                'added_by'  => auth()->user()->id,
            ]);
        }

         // Map roles to corresponding statuses
        $statusMap = [
            'order collection manager' => 1,    // Role for order collection
            'inventory manager' => 2,  // Role for inventory issuance
            'qc manager' => 3,         // Role for quality control
            'packing & dispatch manager' => 4,    // Role for packing and dispatch
            'autidor'                    => 5    // Role for packing and dispatch
        ];

        if (array_key_exists($userRole, $statusMap)) {
            // Update the order status based on the role
            $order->update(['status' => $statusMap[$userRole]]);

            if($userRole == 'inventory manager'){
                $issuance = StoreIssuance::create([
                    'order_id'  => $request->id,
                    'added_by'  => auth()->user()->id,
                ]);
                foreach( $order->items as $product ){
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

        }     else if($userRole == 'admin'){

            if( $order->status == '1'){
                $issuance = StoreIssuance::create([
                    'order_id'  => $request->id,
                    'added_by'  => auth()->user()->id,
                ]);
                foreach( $order->items as $product ){
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

            $order->increment('status');
        }
        return response()->json(['message' => 'Order status updated successfully.'], 200);
    }

    public function reject( Request $request ){

        $order = Order::with('items')->find($request->id);
        if( auth()->user()->role == 'admin'){
            $order->update(['status' => '7']);
            OrderActivity::create([
                'order_id'  => $request->id,
                'activity'  => 'Order rejected',
                'added_by'  => auth()->user()->id,
            ]);

            $srn = StoreReturn::create([
                'order_id'        => $order->id,
                'dropshipper_id'  => $order->belongs_to,
                'remarks'         => 'Rejected',
                'added_by'        => auth()->user()->id
            ]);

            foreach($order->items as $product){
                StoreReturnDetail::create([
                    'srn_id'     => $srn->id,
                    'product_id' => $product->product_variation_id,
                    'quantity'   => $product->quantity,
                    'price'      => $product->price,
                    'total'      => (float)$product->quantity * (float)$product->price,
                    'added_by'   => auth()->user()->id
                ]);

                ProductVariation::where('id', $product->product_variation_id)->increment('stock', $product->quantity);
            }
        }else{
            $order->update(['status' => '6']);
            OrderActivity::create([
                'order_id'  => $request->id,
                'activity'  => 'Order sent for final approval from admin to reject',
                'added_by'  => auth()->user()->id,
            ]);
        }



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

    public function commentAttachment($image){

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
