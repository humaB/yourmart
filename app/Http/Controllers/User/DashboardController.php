<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Product\Category;
use App\Models\Inventory\Product\Tag;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReturnDetail;
use App\Models\User;
use App\Models\User\DropShipper;
use App\Models\User\Supplier;
use Carbon\Carbon;
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


        $approvedDropshipper = $this->getApprovedDropshippers( $request );
        $activeSeller        = $this->getActiveSeller($request);
        $liveProduct        = $this->getLiveProducts();
        $orderProcessed     = $this->orderProcessed($orders);

        $data = [
            'totalOrder'    => $orders->count(),
            'inProcess'     => $orders->whereNotIn('status', [9, 10, 7, 8])->count(),
            'outOfDelivery' => $orders->where('status', '11')->count(),
            'delivered'     => $orders->where('status', '8')->count(),
            'returns'       => $orders->whereIn('status', [9, 10])->count(),

            'normalOrders'  => $orders->where('type', 'Normal')->count(),
            'darazOrders'   => $orders->where('type', 'Daraz')->count(),
            'cashOrders'    => $orders->where('type', 'Cash')->count(),

            'approvedDropshipper' => $approvedDropshipper,
            'activeSeller'       => $activeSeller,
            'liveProduct'        => $liveProduct,
            'orderProcessed'     => $orderProcessed,

        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    private function orderProcessed( $orders){
        $processed = $orders->whereNotIn('status',['6','7'])->count();
        $returns = $orders->whereIn('status',['9','10'])->count();

        $items = OrderItem::whereIn('order_id', $orders->where('status', '8')->pluck('id'))->get();

        // Calculate product cost (quantity * avg_price)
        $itemsWithProductCost = $items->map(function ($item) {
            $quantity = $item->quantity;
            $avgPrice = $item->variation->avg_price ?? 0; // Use 0 if avg_price is null
            $productCost = (float)$quantity * (float)$avgPrice;

            // Add the product code to each item for reference
            $item->product_cost = $productCost;

            return $item;
        });

        // Sum up the product_cost values
        $totalProductCostSum = $itemsWithProductCost->sum('product_cost');

        $grossSales = $items->map(function ($item) {
            $quantity = $item->quantity;
            $price = $item->price ?? 0;
            $productSellingCost = (float)$quantity * (float)$price;

            // Add the product code to each item for reference
            $item->gross_sale = $productSellingCost;

            return $item;
        });

        // Sum up the product_cost values
        $totalGrossSale = $grossSales->sum('gross_sale');

        $packingCharges = $orders->where('status', '8')->sum('packaging_price');
        $packingChargeProfit = $orders->where('status', '8')->sum('packaging_price') * 0.15;

        $courier = $orders->where('status', '8')->sum('courier_service_price');
        $courierProfit = $orders->where('status', '8')->sum('courier_service_internal_price');

        $totalCost   = $totalProductCostSum;
        $grossProfit = $totalGrossSale - $totalCost;

        $returnRatio = $processed > 0 ? ($returns / $processed) * 100 : 0;

        return [
            'processed'           => $processed,
            'totalGrossSale'      => $totalGrossSale,
            'totalCost'           => $totalCost,
            'packing'       => $packingCharges,
            'packingProfit' => $packingChargeProfit,
            'courier'       => $courier,
            'courierProfit' => $courierProfit,
            'costOfGood'    => $totalCost,
            'grossProfit'   => $grossProfit,
            'totalProductCostSum' => $totalProductCostSum,
            'returnRatio'         => round($returnRatio)
        ];
    }

    private function getLiveProducts(){

        $products = Product::where('status', '0')->pluck('id');

        $variations = ProductVariation::whereIn('product_id', $products)
        ->where('status', '0')
        ->where('stock', '>', '0')->get(['id', 'stock', 'avg_price']);

        $currentStockValue = 0;
        foreach( $variations as $variation ){
            $currentStockValue += (float)$variation->stock * (float)$variation->avg_price;
        }

        return [
            'count'             => $products->count(),
            'currentStockValue' => $currentStockValue
        ];

    }

    private function getApprovedDropshippers( $request ){
        $from = $request->from;
        $to = $request->to;

        $currentCount = DropShipper::where('status', '1')
        ->when($from, function ($q) use ($from) {
            $q->whereDate('created_at', '>=', $from);
        })
        ->when($to, function ($q) use ($to) {
            $q->whereDate('created_at', '<=', $to);
        })
        ->count();

        // Calculate previous time range
        $previousFrom = Carbon::parse($from)->subDays(Carbon::parse($from)->diffInDays($to))->toDateString();
        $previousTo = Carbon::parse($to)->subDays(Carbon::parse($from)->diffInDays($to))->toDateString();

        $previousCount = DropShipper::where('status', '1')
            ->whereDate('created_at', '>=', $previousFrom)
            ->whereDate('created_at', '<=', $previousTo)
            ->count();

        // Calculate difference and percentage
        $difference = $currentCount - $previousCount;
        $percentageChange = $previousCount > 0 ? ($difference / $previousCount) * 100 : null;

        // Determine increase or decrease
        $trend = $difference > 0 ? 'increase' : ($difference < 0 ? 'decrease' : 'no change');

        return [
            'current_count' => $currentCount,
            'previous_count' => $previousCount,
            'difference' => $difference,
            'percentage_change' => $percentageChange !== null ? round($percentageChange, 2) . '%' : 'N/A',
            'trend' => $trend,
        ];
    }

    private function getActiveSeller( $request ){
        $from = $request->from;
        $to = $request->to;

        $orders = Order::pluck('belongs_to');
        $currentCount = DropShipper::where('status', '1')
        ->when($from, function ($q) use ($from) {
            $q->whereDate('created_at', '>=', $from);
        })
        ->when($to, function ($q) use ($to) {
            $q->whereDate('created_at', '<=', $to);
        })
        ->whereIn('user_id', $orders)
        ->count();

        // Calculate previous time range
        $previousFrom = Carbon::parse($from)->subDays(Carbon::parse($from)->diffInDays($to))->toDateString();
        $previousTo = Carbon::parse($to)->subDays(Carbon::parse($from)->diffInDays($to))->toDateString();

        $previousCount = DropShipper::where('status', '1')
            ->whereDate('created_at', '>=', $previousFrom)
            ->whereDate('created_at', '<=', $previousTo)
            ->whereIn('user_id', $orders)
            ->count();

        // Calculate difference and percentage
        $difference = $currentCount - $previousCount;
        $percentageChange = $previousCount > 0 ? ($difference / $previousCount) * 100 : null;

        // Determine increase or decrease
        $trend = $difference > 0 ? 'increase' : ($difference < 0 ? 'decrease' : 'no change');

        return [
            'current_count' => $currentCount,
            'previous_count' => $previousCount,
            'difference' => $difference,
            'percentage_change' => $percentageChange !== null ? round($percentageChange, 2) . '%' : 'N/A',
            'trend' => $trend,
        ];
    }

    public function categoryTagWiseProduct(){

        $categoryWiseProducts = Category::withCount('product')->where('parent_id', '0')->get();

        $tagWiseProducts = Tag::withCount('tagged')->get();

        $fastMovingProducts = Product::with('variation')
            ->withSum('issuance', 'total')
            ->withSum('issuance', 'quantity')
            ->orderBy('issuance_sum_quantity', 'desc')
            ->limit(10)
            ->get();

        $slowMovingProducts = Product::with('variation')
            ->withSum('issuance', 'total')
            ->withSum('issuance', 'quantity')
            ->orderBy('issuance_sum_quantity', 'asc')
            ->limit(10)
            ->get();

        $lowStock = Product::with('variation')
            ->withSum('issuance', 'total')
            ->withSum('issuance', 'quantity')
            ->withSum('variation', 'stock')
            ->orderBy('variation_sum_stock', 'asc')
            ->limit(10)
            ->get();

        $highStock = Product::with('variation')
            ->withSum('issuance', 'total')
            ->withSum('issuance', 'quantity')
            ->withSum('variation', 'stock')
            ->orderBy('variation_sum_stock', 'desc')
            ->limit(10)
            ->get();


        $data = [
            'categoryWiseProducts' => $categoryWiseProducts,
            'tagWiseProducts'      => $tagWiseProducts,
            'fastMovingProducts'   => $fastMovingProducts,
            'slowMovingProducts'   => $slowMovingProducts,
            'lowStock'            => $lowStock,
            'highStock'            => $highStock
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
