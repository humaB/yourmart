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
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'inventory manager' && auth()->user()->role != 'supervisor' && auth()->user()->role != 'auditor'){
            abort(401);
        }
        return view('inventory.store.return.courier_return');
    }

    public function record()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'inventory manager' && auth()->user()->role != 'supervisor' && auth()->user()->role != 'auditor'){
            abort(401);
        }
        return view('inventory.store.return.courier_return_record');
    }

    public function inwardRecord(Request $request){

        $data = StoreReturnDetail::with('product', 'srn.order')
        ->when( $request->from, function ($query, $from) {
            return $query->whereDate('created_at', '>=', $from);
        })
        ->when( $request->to, function ($query, $to) {
            return $query->whereDate('created_at', '<=', $to);
        })
        ->when($request->courier, function ($query, $courier) {
            return $query->whereHas('srn.order', function ($q) use ($courier) {
                $q->where('courier_service_id', $courier);
            });
        })
        ->orderBy('id','desc')
        ->get();

        return (new ResponseCollection($data))
        ->response()
        ->setStatusCode(200);
    }


    public function pendingReturns( Request $request ){

        $data = Order::with('user', 'shop', 'courier','items.variation.product')->where('status', 9)
            ->when( $request->from, function ($query, $from) {
                return $query->whereDate('created_at', '>=', $from);
            })
            ->when( $request->to, function ($query, $to) {
                return $query->whereDate('created_at', '<=', $to);
            })
            ->when( $request->courier, function ($query, $courier) {
                return $query->where('courier_service_id', $courier);
            })
            ->orderBy('id', 'desc')
            ->get();

        return (new ResponseCollection($data))
        ->response()
        ->setStatusCode(200);
    }

    public function returnProduct( Request $request ){

        $order = Order::where('id', $request->id)->first();
        $order->update([
            'status' => '10'
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
