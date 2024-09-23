<?php

namespace App\Http\Controllers\Inventory\Order;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderComment;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('inventory.product.order.orders');
    }

    public function fetchOrders(){
        $userRole  = auth()->user()->role;
          // Map roles to corresponding statuses
        $statusMap = [
            'order collection manager' => 0,    // Role for order collection
            'inventory manager' => 1,  // Role for inventory issuance
            'qc manager' => 2,         // Role for quality control
            'packing & dispatch manager' => 3    // Role for packing and dispatch
        ];


        if( $userRole != 'admin'){
            $orders = Order::with('user')->where('status', $statusMap[$userRole])->get();
        }else{
            $orders = Order::with('user')->get();
        }

        return (new ResponseCollection($orders))
            ->response()
            ->setStatusCode(200);
    }

    public function details(Request $request)
    {

        $orders = Order::with('city', 'shop', 'user', 'items.variation.product', 'comments.user', 'items.variation.images.attachment', 'items.variation.color', 'items.variation.size')->where('id', $request->id)->get();

        return (new ResponseCollection($orders))
            ->response()
            ->setStatusCode(200);
    }

    public function updateStatus( Request $request ){
        $userRole  = auth()->user()->role;
        $order = Order::with('items')->find($request->id);

         // Map roles to corresponding statuses
        $statusMap = [
            'order collection manager' => 1,    // Role for order collection
            'inventory manager' => 2,  // Role for inventory issuance
            'qc manager' => 3,         // Role for quality control
            'packing & dispatch manager' => 4    // Role for packing and dispatch
        ];

        if (array_key_exists($userRole, $statusMap)) {
            // Update the order status based on the role
            $order->update(['status' => $statusMap[$userRole]]);

            if($userRole == 'inventory manager'){
                foreach( $order->items as $product ){
                    ProductVariation::where('id', $product->product_variation_id)->decrement('stock', $product->quantity);
                }
            }

            return response()->json(['message' => 'Order status updated successfully.'], 200);
        }
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
