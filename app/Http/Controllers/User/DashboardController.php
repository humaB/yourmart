<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReturnDetail;
use App\Models\User;
use App\Models\User\DropShipper;
use App\Models\User\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function fetchData(Request $request){

        $orders = Order::when($request->from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from);
        })
        ->when($request->to, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to);
        })->get();

        $items = OrderItem::whereIn('order_id', $orders->where('status', '8')->pluck('id'))->get();

        // Calculate product code (quantity * avg_price)
        $itemsWithProductCost = $items->map(function ($item) {
            $quantity = $item->quantity;
            $avgPrice = $item->variation->avg_price ?? 0; // Use 0 if avg_price is null
            $productCost = $quantity * $avgPrice;

            // Add the product code to each item for reference
            $item->product_cost = $productCost;

            return $item;
        });

        // Sum up the product_cost values
        $totalProductCostSum = $itemsWithProductCost->sum('product_cost');

        $grossSales = $items->map(function ($item) {
            $quantity = $item->quantity;
            $avgPrice = $item->price ?? 0; // Use 0 if avg_price is null
            $productCost = $quantity * $avgPrice;

            // Add the product code to each item for reference
            $item->gross_sale = $productCost;

            return $item;
        });

        // Sum up the product_cost values
        $totalGrossSale = $itemsWithProductCost->sum('gross_sale');

        $packingCharges = $orders->where('status', '8')->sum('packaging_price');
        $packingChargeProfit = $orders->where('status', '8')->sum('packaging_price') * 0.15;

        $courier = $orders->where('status', '8')->sum('courier_service_price');
        $courierProfit = $orders->where('status', '8')->sum('courier_service_internal_price');

        $totalCost   = $totalProductCostSum + ($packingCharges - $packingChargeProfit) + ($courier - $courierProfit);
        $grossProfit = $totalGrossSale - $totalCost;

        $data = [
            'totalOrder'    => $orders->count(),
            'inProcess'     => $orders->whereNotIn('status', [9, 10, 7, 8])->count(),
            'outOfDelivery' => $orders->where('status', '11')->count(),
            'delivered'     => $orders->where('status', '8')->count(),
            'returns'       => $orders->whereIn('status', [9, 10])->count(),

            'normalOrders'  => $orders->where('type', 'Normal')->count(),
            'darazOrders'   => $orders->where('type', 'Daraz')->count(),
            'cashOrders'    => $orders->where('type', 'Cash')->count(),

            'grossSales'    => $totalGrossSale,
            'itemSolds'     => $items->sum('quantity'),
            'productCost'   => $totalProductCostSum,
            'packing'       => $packingCharges,
            'packingProfit' => $packingChargeProfit,
            'courier'       => $courier,
            'courierProfit' => $courierProfit,
            'costOfGood'    => $totalCost,
            'grossProfit'   => $grossProfit
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
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

    public function topTenDropshipper( Request $request ){

        $dropshippers = User::withCount('deliveredOrders as total_orders')
            ->withCount('returnedOrders as total_returns')
            ->with('deliveredOrders')
            ->with('dropshipper.shops')
            ->orderBy('total_orders', 'desc')
            ->take(10)
        ->when($request->from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from);
        })
        ->when($request->to, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to);
        })
        ->get();

        $orders = Order::when($request->from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from);
        })
        ->when($request->to, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to);
        })
        ->whereIn('status', ['8','9','10'])
        ->get();

        $data = [
            'dropshippers' => $dropshippers,

            'dropshipperPayouts' => [
                'total' => $orders->sum('total_profit'),
                'paid' => $orders->sum('total_paid_profit'),
                'remaining' => $orders->sum('total_profit') - $orders->sum('total_paid_profit'),
                'total_sellers' => DropShipper::
                    when($request->from, function ($q) use ($request) {
                        $q->whereDate('created_at', '>=', $request->from);
                    })
                    ->when($request->to, function ($q) use ($request) {
                        $q->whereDate('created_at', '<=', $request->to);
                    })->select('status')->count()
            ],

            'dropshipperApplication' => DropShipper::
                when($request->from, function ($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->from);
                })
                ->when($request->to, function ($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->to);
                })->select('status')->get(),

            'shipperApplication' => Supplier::when($request->from, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->to, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })->select('status')->get(),
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }
}
