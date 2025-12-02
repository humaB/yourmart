<?php

namespace App\Http\Controllers;

use App\Services\ProfitService;
// use App\Http\Controllers\Controller;
// use App\Http\Resources\ResponseCollection;
// use App\Models\Inventory\Order\Order;
// use App\Models\Inventory\Order\OrderItem;
// use App\Models\User\DropShipper;
// use Illuminate\Http\Request;
// use App\Http\Controllers\User\GraphController;
// use App\Models\Inventory\Store\StoreIssuance;
// use App\Models\Inventory\Store\StoreIssuanceDetail;
// use App\Models\Inventory\Store\StoreReceivedDetail;
// use App\Models\Inventory\Store\StoreReturn;
// use App\Models\Inventory\Store\StoreReturnDetail;
// use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\User\DropShipper;
use Illuminate\Http\Request;
use App\Http\Controllers\User\GraphController;
use App\Models\Inventory\Store\StoreIssuance;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReceivedDetail;
use App\Models\Inventory\Store\StoreReturn;
use App\Models\Inventory\Store\StoreReturnDetail;
use Illuminate\Support\Facades\DB;

class GrowthController extends Controller
{
    protected $graphController;
    protected $profitService;

    public function __construct(GraphController $graphController, ProfitService $profitService)
    {
        $this->graphController = $graphController;
        $this->profitService = $profitService;
    }

    public function index()
    {
        return view('growthdashboard');
    }

    public function fetchData(Request $request)
    {
        // Get today's data
        $todaysData = $this->getTodaysData();

        // Get graph data from GraphController
        // $graphController = new GraphController();
        // $dashboardGraphs = $graphController->getDashboardGraphs();
        // $dropshipperGraphLast120Days = $graphController->dropshipperGraphLast120Days();
        // $activeSellersMonthly = $graphController->activeSellersMonthly();

        $dashboardGraphs = $this->graphController->getDashboardGraphs();
        $dropshipperGraphLast120Days = $this->graphController->dropshipperGraphLast120Days();
        $activeSellersMonthly = $this->graphController->activeSellersMonthly();
        

        $data = [
            'todaysData' => $todaysData,
            'dashboardGraphs' => $dashboardGraphs,
            'dropshipperGraphLast120Days' => $dropshipperGraphLast120Days,
            'activeSellersMonthly' => $activeSellersMonthly,
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }
    public function getTodaysData()
{
    $today = now()->format('Y-m-d');
    
    // Get orders
    $orders = Order::whereNotIn('status', ['6', '7'])
        ->whereDate('created_at', $today)
        ->get();
        
    // Get active sellers
    $orderbelongsto = $orders->pluck('belongs_to')->unique()->values();
    $todaysActiveSellerIds = DropShipper::where('status', '1')
        ->whereIn('user_id', $orderbelongsto)
        ->count();
    
    // Calculate returns (same as GraphController)
    $postExReturns = StoreReturnDetail::with('srn.order')
        ->whereDate('created_at', $today)
        ->whereHas('srn.order', function ($q) {
            $q->where('courier_service_id', '2');
        })
        ->count();

    $leopardReturns = StoreReturnDetail::with('srn.order')
        ->whereDate('created_at', $today)
        ->whereHas('srn.order', function ($q) {
            $q->where('courier_service_id', '1');
        })
        ->count();
    
    $returnsCount = $postExReturns + $leopardReturns;
    
    // Calculate profit
    // $profit = $this->calculateDailyOrderIssuanceProfit($today);
    $profit = $this->profitService->calculateDailyOrderIssuanceProfit($today);
    
    return [
        'orders' => $orders->count(),
        'sales' => $orders->sum('total_bill'),
        'profit' => (int) $profit,
        'returns' => $returnsCount,
        'todaysRegistrations' => DropShipper::where('status', '1')
            ->whereDate('created_at', $today)
            ->count(),
        'todaysActiveSellers' => $todaysActiveSellerIds,
    ];
}

// private function calculateDailyOrderIssuanceProfit($date)
// {
//     $orders = StoreIssuance::whereDate('created_at', $date)
//         ->where('order_id', '!=', '0')
//         ->pluck('id');

//     $issues = StoreIssuanceDetail::with('product.variation', 'sin')
//         ->whereIn('sin_id', $orders)
//         ->get()
//         ->groupBy('product_id');

//     $totalProfit = 0;

//     foreach ($issues as $group) {
//         $quantity = $group->sum('quantity');
//         $productId = $group[0]->product_id;

//         // Purchase Rate
//         $purchase = StoreReceivedDetail::where('created_at', '<=', $date)
//             ->where('product_id', $productId)
//             ->select(DB::raw("SUM(total) / SUM(quantity) as rate"))
//             ->first();

//         $purchaseRate = round($purchase->rate ?? 0);

//         // Issuance
//         $totalIssuance = $group->sum('total');
//         $avgIssuancePrice = $quantity > 0 ? round($totalIssuance / $quantity) : 0;

//         // Returns
//         $returnedQty = 0;
//         foreach ($group as $item) {
//             $orderNo = $item->sin->order_id;
//             $returned = StoreReturn::where('order_id', $orderNo)->first();
//             if ($returned) {
//                 $r = StoreReturnDetail::where('product_id', $productId)
//                     ->where('srn_id', $returned->id)
//                     ->first();
//                 $returnedQty += $r ? $r->quantity : 0;
//             }
//         }

//         $netQuantity = $quantity - $returnedQty;
//         $netSale = $netQuantity * $avgIssuancePrice;
//         $netPurchase = $netQuantity * $purchaseRate;
//         $profit = $netSale - $netPurchase;

//         $totalProfit += $profit;
//     }

//     return (int) $totalProfit;
// }
    
}