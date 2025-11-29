<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
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
    public function index()
    {
        return view('growthdashboard');
    }

    public function fetchData(Request $request)
    {
        // Get today's data
        $todaysData = $this->getTodaysData();

        // Get graph data from GraphController
        $graphController = new GraphController();
        $dashboardGraphs = $graphController->getDashboardGraphs();
        $dropshipperGraphLast120Days = $graphController->dropshipperGraphLast120Days();
        $activeSellersMonthly = $graphController->activeSellersMonthly();
        

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
        //   $from = $request->from;
        // $to = $request->to;
        $today = now()->format('Y-m-d');
        
        $orders = Order::whereNotIn('status', ['6', '7'])
            ->whereDate('created_at', $today)
            ->get();
$orderbelongsto = $orders->pluck('belongs_to')->unique()->values();
 $todaysActiveSellerIds = DropShipper::where('status', '1')->whereIn('user_id', $orderbelongsto)
            ->count();
        $returns = StoreReturnDetail::whereDate('created_at', $today)->count();
        $profit = $this->calculateDailyOrderIssuanceProfit($today);
        $todaysRegistrations = DropShipper::where('status', '1')
        ->whereDate('created_at', $today)
        ->count();
        return [
            'orders' => $orders->count(),
            'sales' => $orders->sum('total_bill'),
            'profit' => (int) $profit,
            'returns' => $returns,
            'todaysRegistrations' => $todaysRegistrations,
            'todaysActiveSellers' => $todaysActiveSellerIds,
        ];
    }
    private function calculateDailyOrderIssuanceProfit($date)
    {
        $orders = StoreIssuance::whereDate('created_at', $date)
            ->where('order_id', '!=', '0')
            ->pluck('id');
    
        $issues = StoreIssuanceDetail::with('product.variation', 'sin')
            ->whereIn('sin_id', $orders)
            ->get()->groupBy('product_id');
    
        $totalProfit = 0;
    
        foreach ($issues as $singleProductGroup) {
            $quantity = $singleProductGroup->sum('quantity');
    
            // Calculate Avg Purchase Price
            $rate = StoreReceivedDetail::where('created_at', '<=', $date)
                ->where('product_id', $singleProductGroup[0]->product_id)
                ->select(DB::raw("SUM(total) / SUM(quantity) as rate"))
                ->first();
    
            $purchaseRate = $rate->rate ?? 0;
    
            // Calculate Avg Issuance Price
            $issancePrice = $singleProductGroup->sum('total');
            $avgIssuancePrice = $quantity > 0 ? ($issancePrice / $quantity) : 0;
    
            // Get Return quantity
            $returnQuantity = 0;
            foreach ($singleProductGroup as $order) {
                $orderNo = $order->sin->order_id;
                $productId = $order->product_id;
    
                $returned = StoreReturn::where('order_id', $orderNo)->first();
                if ($returned) {
                    $returnRecord = StoreReturnDetail::where('product_id', $productId)
                        ->where('srn_id', $returned->id)
                        ->first();
                    $returnQuantity += $returnRecord ? $returnRecord->quantity : 0;
                }
            }
            $netQuantity = $quantity - $returnQuantity;
            $netSale = $avgIssuancePrice * $netQuantity;
            $netPurchase = $netQuantity * $purchaseRate;
            $profit = $netSale - $netPurchase;
    
            $totalProfit += $profit;
        }
    
        return round($totalProfit, 2);
    }
    
}