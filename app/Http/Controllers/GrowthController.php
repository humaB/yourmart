<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Store\StoreReturnDetail;
use App\Models\User\DropShipper;
use Illuminate\Http\Request;
use App\Http\Controllers\User\GraphController;

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

        $data = [
            'todaysData' => $todaysData,
            'dashboardGraphs' => $dashboardGraphs,
            'dropshipperGraphLast120Days' => $dropshipperGraphLast120Days,
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function getTodaysData()
    {
        $today = now()->format('Y-m-d');
        
        $orders = Order::whereNotIn('status', ['6', '7'])
            ->whereDate('created_at', $today)
            ->get();

        $returns = StoreReturnDetail::whereDate('created_at', $today)->count();
        $profit = $this->calculateDailyOrderIssuanceProfit($today);
        $todaysRegistrations = DropShipper::where('status', '1')
        ->whereDate('created_at', $today)
        ->count();

        return [
            'orders' => $orders->count(),
            'sales' => $orders->sum('total_bill'),
            'profit' => $profit,
            'returns' => $returns,
            'todaysRegistrations' => $todaysRegistrations
        ];
    }

    private function calculateDailyOrderIssuanceProfit($date)
    {
        try {
            // Get orders for the specific date
            $orders = Order::whereNotIn('status', ['6', '7'])
                ->whereDate('created_at', $date)
                ->get();

            if ($orders->isEmpty()) {
                return 0;
            }

            $totalProfit = 0;

            foreach ($orders as $order) {
                // Get order items
                $orderItems = OrderItem::where('order_id', $order->id)->get();
                
                $productCost = 0;
                $sellingPrice = 0;

                foreach ($orderItems as $item) {
                    $avgPrice = $item->variation->avg_price ?? 0;
                    $productCost += $item->quantity * $avgPrice;
                    $sellingPrice += $item->quantity * $item->price;
                }

                // Calculate profit for this order
                $orderProfit = $sellingPrice - $productCost;
                $totalProfit += $orderProfit;
            }

            return $totalProfit;

        } catch (\Exception $e) {
            \Log::error('Profit Calculation Error: ' . $e->getMessage());
            return 0;
        }
    }
}