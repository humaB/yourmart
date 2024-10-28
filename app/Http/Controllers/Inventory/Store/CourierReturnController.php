<?php

namespace App\Http\Controllers\Inventory\Store;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\Store\StoreReturn;
use App\Models\Inventory\Store\StoreReturnDetail;
use Illuminate\Http\Request;

class CourierReturnController extends Controller
{
    public function index()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'inventory manager' && auth()->user()->role != 'supervisor'){
            abort(401);
        }
        return view('inventory.store.return.courier_return');
    }

    public function record()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'inventory manager' && auth()->user()->role != 'supervisor'){
            abort(401);
        }
        return view('inventory.store.return.courier_return_record');
    }

    public function inwardRecord(){

        $data = StoreReturnDetail::with('variation.product', 'srn.order')
        ->orderBy('id','desc')
        ->get();

        return (new ResponseCollection($data))
        ->response()
        ->setStatusCode(200);
    }


    public function pendingReturns(){

        $data = Order::with('user', 'shop')->where('status', 9)
        ->get();


        return (new ResponseCollection($data))
        ->response()
        ->setStatusCode(200);
    }

    public function returnProduct( Request $request ){

        $order = Order::where('id', $request->id)->first();
        $order->update([
            'status' => 10
        ]);

        $srn = StoreReturn::create([
            'order_id'        => $order->id,
            'dropshipper_id'  => $order->belongs_to,
            'remarks'         => 'Returned from courier',
            'return_type'     => '1',
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

        return ['message' => 'Successfully added to stock'];

    }
}
