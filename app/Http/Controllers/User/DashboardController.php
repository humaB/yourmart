<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\User;
use App\Models\User\DropShipper;
use App\Models\User\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function fetchData(){

    }

    public function topSellingProduct(){

        $orders = Order::where('status', '8')->pluck('id');

        $top10SellingProducts = OrderItem::with('variation.product')
           ->whereIn('order_id', $orders)
           ->select('product_variation_id', DB::raw('SUM(price * quantity) as selling_price'), DB::raw('SUM(quantity) as total_quantity'))
           ->groupBy('product_variation_id')
           ->orderByDesc('total_quantity')
           ->get();

           return (new ResponseCollection($top10SellingProducts))
               ->response()
               ->setStatusCode(200);
    }

    public function topTenDropshipper(){

        $dropshippers = User::withCount('deliveredOrders as total_orders')
        ->withCount('returnedOrders as total_returns')
        ->with('deliveredOrders')
        ->with('dropshipper.shops')
        ->orderBy('total_orders', 'desc')
        ->take(10)
        ->get();

        $data = [
            'dropshippers' => $dropshippers,
            'dropshipperApplication' => DropShipper::select('status')->get(),
            'shipperApplication'     => Supplier::select('status')->get(),
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }
}
