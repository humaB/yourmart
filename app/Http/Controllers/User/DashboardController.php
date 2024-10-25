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

         $productsWithMaxPrice = DB::table('inventory_purchase_order_store_received_details')
         ->select('product_id', DB::raw('MAX(price) as max_price'))
         ->groupBy('product_id')
         ->get();


            foreach($productsWithMaxPrice as $setPrice) {
                $product = Product::with('variation')->find($setPrice->product_id);

                if ($product && filled($product->variation)) {
                    ProductVariation::where('id', $product->variation->id)->update([
                        'avg_price' => $setPrice->max_price
                    ]);
                }
            }



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
