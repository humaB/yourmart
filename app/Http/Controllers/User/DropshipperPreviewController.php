<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperShop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DropshipperPreviewController extends Controller
{
    public function fetchData(Request $request )
    {

        $dropshipper = Dropshipper::where('id', $request->id)->first();
        $totalProfit = $dropshipper->total_payable;
        $totalRemaining = $dropshipper->remaining_amount;
        $totalPaid = $dropshipper->total_paid;

        $orders = Order::where('belongs_to', $dropshipper->user_id)
            ->whereNotIn('status', [9, 10, 7])
            ->where('is_replacement', '0')
            ->get();

        $confirmedOrders = Order::where('belongs_to', $dropshipper->user_id)
            ->where('status', 8)
            ->where('is_replacement', '0')
            ->get();

        $totalOrders = $orders->count();
        $totalSales = $confirmedOrders->sum('selling_price') + $confirmedOrders->sum('advance_amount');
        $totalProductCost = $confirmedOrders->sum('total_bill') - ($confirmedOrders->sum('packaging_price') + $confirmedOrders->sum('courier_service_price'));
        $totalPackingCourier =  $confirmedOrders->sum('packaging_price') + $confirmedOrders->sum('courier_service_price');

        $outFordeliveredOrders  = Order::where('belongs_to', $dropshipper->user_id)->where('is_replacement', '0')->where('status', '11')->count();
        $deliveredOrders  = Order::where('belongs_to', $dropshipper->user_id)->where('is_replacement', '0')->where('status', '8')->count();
        $inProcessOrder   = Order::where('belongs_to', $dropshipper->user_id)
            ->whereNotIn('status', [9, 10, 7, 8])
            ->where('is_replacement', '0')
            ->count();
        $failedOrder  = Order::where('belongs_to', $dropshipper->user_id)->where('is_replacement', '0')->whereIn('status', [9, 10])->count();

        $revenueGraphData = [
            [
                'name' => 'No of Orders',
                'data' => array_values($confirmedOrders->groupBy(function ($order) {
                    return \Carbon\Carbon::parse($order->created_at)->format('Y-m-d'); // Group by date only
                })->map(function ($orders, $date) {
                    return $orders->count(); // Count of sales per day
                })->toArray()), // Use array_values to convert it to an indexed array
            ],
            [
                'name' => 'Profit',
                'data' => array_values($confirmedOrders->groupBy(function ($order) {
                    return \Carbon\Carbon::parse($order->created_at)->format('Y-m-d'); // Group by date only
                })->map(function ($orders, $date) {
                    return $orders->sum('selling_price') - $orders->sum('total_bill'); // Calculate profit per day
                })->toArray()), // Use array_values to convert it to an indexed array
            ],
        ];

        $revenueDates = $confirmedOrders->groupBy(function ($order) {
            return \Carbon\Carbon::parse($order->created_at)->format('Y-m-d'); // Group by date only
        })->keys()->toArray(); // Get only the unique dates

        //Month Wise Revence
        $barChart = $this->barChatData($dropshipper);

        $topFiveProducts = $this->topSaleProducts($confirmedOrders->pluck('id'));

        $leopardPerformance = $this->leopardPerformance($dropshipper);

        $accountHealth = $this->accountHealth( $deliveredOrders,  $failedOrder );

        $stores = $this->storeAccountHealth( $dropshipper );


        $totalCustomers = $confirmedOrders->groupBy('phone_number')->map(function ($orders) {
            $orderCount = $orders->count();
            $totalSales = $orders->sum('selling_price');
            $totalProfit = $orders->sum('selling_price') - $orders->sum('total_bill');

            return [
                'name'        => $orders->first()->customer_name, // Use the first order's customer name
                'contact'     => $orders->first()->phone_number,
                'order_count' => $orderCount,
                'total_sales' => $totalSales,
                'total_profit' => $totalProfit,
            ];
        })->values()->toArray(); // values() resets the keys, toArray() converts it to a standard array
        // Build the response data array
        $data = [
            'totalProfit' => $totalProfit,
            'totalRemaining' => $totalRemaining,
            'totalPaid' => $totalPaid,

            'totalOrders' => $totalOrders,
            'totalSales' => $totalSales,
            'totalProductCost' => $totalProductCost,
            'totalPackingCourier' => $totalPackingCourier,

            'deliveredOrders' => $deliveredOrders,
            'inProcessOrder' => $inProcessOrder,
            'failedOrder' => $failedOrder,
            'outFordeliveredOrders' => $outFordeliveredOrders,

            'topFiveProducts' =>  $topFiveProducts,

            'name'        => $dropshipper->full_name,
            'stores'      => $stores,

            'revenueGraphData'  => $revenueGraphData,
            'revenueDates'      => $revenueDates,
            'barChart'          => $barChart,
            'accountHealth'     => $accountHealth,

            'leopardPerformance' => $leopardPerformance,
            'totalCustomers' => $totalCustomers
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    private function barChatData( $dropshipper ){
        $last12Months = [];
        $startDate = now()->subMonths(11); // 11 months ago (to include current month)

        for ($i = 0; $i < 12; $i++) {
            $month = $startDate->clone()->addMonths($i);
            $last12Months[] = [
                'country' => $month->format('Y') . ' ' . $month->format('F'),
                'visits' => 0,
            ];
        }

        $ordersData = Order::where('belongs_to', $dropshipper->user_id)
            ->where('status', 8)
            ->whereDate('created_at', '>=', now()->subMonths(12))
            ->selectRaw("YEAR(created_at) as year, MONTHNAME(created_at) as month, SUM(selling_price - total_bill) as visits")
            ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at), MONTHNAME(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
            ->get()
            ->map(function ($item) {
                return [
                    'country' => $item->year . ' ' . $item->month,
                    'visits' => $item->visits,
                ];
            })
            ->toArray();

        // Merge data
        $mergedData = [];
        foreach ($last12Months as $month) {
            $found = false;
            foreach ($ordersData as $order) {
                if ($month['country'] == $order['country']) {
                    $mergedData[] = $order;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $mergedData[] = $month;
            }
        }

        // Sort by month
        usort($mergedData, function($a, $b) {
            return strtotime($a['country']) - strtotime($b['country']);
        });

        $barChart = $mergedData;

        return $barChart;
    }

    private function storeAccountHealth( $dropshipper ){
        $stores = DropshipperShop::withCount('delivered_orders', 'returned_orders')
            ->where('dropshipper_id', $dropshipper->id)
            ->get();

        // Example to loop through stores and access the counts
        foreach ($stores as $store) {
            $deliveredCount = $store->delivered_orders_count; // Count of delivered orders
            $returnedCount = $store->returned_orders_count;   // Count of returned orders

            // You can now use these counts for further logic, e.g., calculating account health
            $totalOrders = $deliveredCount + $returnedCount;

            if ($totalOrders > 0) {
                $accountHealth = ($deliveredCount / $totalOrders) * 100;
            } else {
                $accountHealth = 0;
            }
            // Assign the calculated health to the store object
            $store->health = round($accountHealth);
        }

        return $stores;
    }

    private function accountHealth($totalDelivered, $totalReturned){

        // Assume Delivered Orders 10
        // Assume Return 5

        // A/C Health % = (10/(10+5))*100
        //                        = 66%

        // Calculate account health percentage
        $totalOrders = $totalDelivered + $totalReturned;

        // Avoid division by zero if no orders exist
        if ($totalOrders > 0) {
            $accountHealth = ($totalDelivered / $totalOrders) * 100;
        } else {
            $accountHealth = 0; // If no orders, health is 0
        }

        return round($accountHealth);
    }

    private function leopardPerformance($dropshipper ,$from = null, $to = null)
    {
        $startDate = Carbon::now()->subDays(6)->startOfDay(); // 7 days back from today
        $endDate = Carbon::now()->endOfDay(); // Today

        // Get delivered orders for the last 7 days
       $deliveredOrders = Order::where('status', '8')
            ->where('belongs_to', $dropshipper->user_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->get();

        // Get returned orders for the last 7 days
        $returnedOrders = Order::whereIn('status', ['9', '10'])
            ->where('belongs_to', $dropshipper->user_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->get();

        // Initialize arrays for labels and datasets
        $labels = [];
        $deliveredData = [];
        $returnedData = [];

        // Process the data for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $day = Carbon::now()->subDays($i)->format('l'); // Get the day name (Sunday, Monday, etc.)

            // Append day name to labels
            $labels[] = $day;

            // Find the count for this date in delivered orders
            $deliveredCount = $deliveredOrders->firstWhere('date', $date)->total ?? 0;
            $deliveredData[] = $deliveredCount;

            // Find the count for this date in returned orders
            $returnedCount = $returnedOrders->firstWhere('date', $date)->total ?? 0;
            $returnedData[] = $returnedCount;
        }

        // Return the formatted data in the structure required by Vue.js charts
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Delivered Orders',
                    'data' => $deliveredData,
                    'backgroundColor' => 'rgba(255,164,38,.9)',
                    'borderColor' => 'rgba(255,164,38,.9)',
                ],
                [
                    'label' => 'Returned Orders',
                    'data' => $returnedData,
                    'backgroundColor' => 'rgba(71,65,98,.9)',
                    'borderColor' => 'rgba(71,65,98,.9)',
                ]
            ]
        ];
    }

    public function topSaleProducts($orders)
    {
        $topProducts = OrderItem::whereIn('order_id', $orders)
            ->with('variation.product')
            ->select(
                'product_variation_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(packaging_cost) as total_packaging_cost'),
                DB::raw('SUM(price * quantity) as total_price'), // Multiplying price by quantity
                DB::raw('SUM(courier_cost) as total_courier_cost'),
                DB::raw('SUM(sell_price) as total_sell_price')
            )
            ->groupBy('product_variation_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(5)
            ->get();


        return $topProducts;
    }
}
