<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function fetchData(){

         $orders = Order::where('status', '8')->pluck('id');

         $top10SellingProducts = OrderItem::with('variation.product')
            ->whereIn('order_id', $orders)
            ->select('product_variation_id', DB::raw('SUM(price * quantity) as selling_price'), DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_variation_id')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

            $data = [
                'top10SellingProducts' => $top10SellingProducts
            ];

            return (new ResponseCollection($data))
                ->response()
                ->setStatusCode(200);
    }
}
