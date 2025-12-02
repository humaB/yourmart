<?php

namespace App\Http\Controllers\User;

use App\Services\ProfitService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Product\Category;
use App\Models\Inventory\Product\Tag;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\PurchaseOrder\PurchaseOrder;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReturnDetail;
use App\Models\Ticket;
use App\Models\User;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperLevel;
use App\Models\User\DropShipperShop;
use App\Models\User\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{

    protected $profitService;
    
    // Add constructor
    public function __construct(ProfitService $profitService)
    {
        $this->profitService = $profitService;
    }


    public function fetchData(Request $request)
    {
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        $orders = Order::when($request->from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from);
        })
            ->when($request->to, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })->get();

             $graphController = new GraphController($this->profitService);
            // $dashboardGraphs = $graphController->getDashboardGraphs();
            // $newproducts30daysgraph = $graphController->newproducts30daysgraph();
            // $dropshipperGraphLast120Days = $graphController->dropshipperGraphLast120Days();
            $ticketTypesGraphData = $graphController->ticketTypesGraphData();



        $approvedDropshipper = $this->getApprovedDropshippers($request);
        $activeSeller        = $this->getActiveSeller($request);
        $liveProduct        = $this->getLiveProducts();
        $orderProcessed     = $this->orderProcessed($orders);
        $pendingPayouts     = $this->pendingPayouts();
        $pendingRequests    = $this->pendingRequests($request);
        $allProcessedOrders = $this->allProcessedOrder($orders, $request);
        $topFiveDropshippers = $this->topFiveDropshippers($request);
        $topFiveSellingProduct = $this->topFiveSellingProduct($request);
        $topFiveSuppliers   = $this->topFiveSuppliers($request);
        $inventoryStatus    = $this->inventoryStatus($request);
        $dropshipperGraph   = $this->dropshipperGraph();
        $revenueOrderGraph  = $this->revenueOrderGraph();
        // 30 days graphs

        // $dropshipperGraphLast120Days = (new DropShipperController())->dropshipperGraphLast120Days();
           // 30 days graphs

        $courierPerformance  = $this->courierPerformance();
        $levels = DropShipperLevel::where('level', '!=', 'New Seller')->get();

        $data = [
            'orders'  => [
                'totalOrder'    => $orders->count(),
                'inProcess'     => $orders->where('type', 'Normal')->whereNotIn('status', [6, 7, 8, 9, 10])->count(),
                'delivered'     => $orders->where('status', '8')->count(),
                'returns'       => $orders->whereIn('status', [9, 10])->count(),
                'returnAmount'  => $orders->whereIn('status', [9, 10])->sum('total_bill'),
            ],

            'approvedDropshipper' => $approvedDropshipper,
            'activeSeller'       => $activeSeller,
            'liveProduct'        => $liveProduct,
            'orderProcessed'     => $orderProcessed,
            'payOuts'            => $pendingPayouts,
            'pendingRequests'    => $pendingRequests,
            'allProcessedOrders' => $allProcessedOrders,
            'topFiveDropshippers' => $topFiveDropshippers,
            'topFiveSellingProduct' => $topFiveSellingProduct,
            'topFiveSuppliers'      => $topFiveSuppliers,
            'inventoryStatus'       => $inventoryStatus,
            'dropshipperGraph'      => $dropshipperGraph,
            'revenueOrderGraph'     => $revenueOrderGraph,

            // 30 days graphs

           // 'dropshipperGraphLast120Days' => $dropshipperGraphLast120Days, // Use old name temporarily
            //'newproducts30daysgraph' => $newproducts30daysgraph,
            //'dashboardGraphs' => $dashboardGraphs,
            'ticketTypesGraphData' => $ticketTypesGraphData ,

            // 30 days graphs

            'levels'                => $levels,

            'courierPerformance'    => $courierPerformance
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }



    private function courierPerformance()
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth(); // 12 months ago
        $endDate = Carbon::now()->endOfMonth(); // current month end

        // Group all orders by courier, month, and status
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['8', '9', '10']) // Delivered, Return, Return to Store
            ->select(
                'courier_service_id',
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('courier_service_id', 'month', 'status')
            ->get();

        // Prepare structure
        $labels = [];
        $leopardRates = [];
        $postexRates = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('F Y');

            $labels[] = $monthLabel;

            // Leopard (courier_service_id = 1)
            $leopardDelivered = $orders->firstWhere(fn($o) => $o->courier_service_id == 1 && $o->status == '8' && $o->month == $monthKey)->total ?? 0;
            $leopardReturned = $orders->firstWhere(fn($o) => $o->courier_service_id == 1 && $o->status == '9' && $o->month == $monthKey)->total ?? 0;
            $leopardReturnToStore = $orders->firstWhere(fn($o) => $o->courier_service_id == 1 && $o->status == '10' && $o->month == $monthKey)->total ?? 0;

            $leopardTotal = $leopardDelivered + $leopardReturned + $leopardReturnToStore;
            $leopardRate = $leopardTotal > 0 ? round(($leopardDelivered / $leopardTotal) * 100, 2) : 0;
            $leopardRates[] = $leopardRate;

            // PostEx (courier_service_id = 2)
            $postexDelivered = $orders->firstWhere(fn($o) => $o->courier_service_id == 2 && $o->status == '8' && $o->month == $monthKey)->total ?? 0;
            $postexReturned = $orders->firstWhere(fn($o) => $o->courier_service_id == 2 && $o->status == '9' && $o->month == $monthKey)->total ?? 0;
            $postexReturnToStore = $orders->firstWhere(fn($o) => $o->courier_service_id == 2 && $o->status == '10' && $o->month == $monthKey)->total ?? 0;

            $postexTotal = $postexDelivered + $postexReturned + $postexReturnToStore;
            $postexRate = $postexTotal > 0 ? round(($postexDelivered / $postexTotal) * 100, 2) : 0;
            $postexRates[] = $postexRate;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Leopard Success Rate (%)',
                    'data' => $leopardRates,
                    'backgroundColor' => 'rgba(255,164,38,.9)',
                    'borderColor' => 'rgba(255,164,38,.9)',
                ],
                [
                    'label' => 'PostEx Success Rate (%)',
                    'data' => $postexRates,
                    'backgroundColor' => 'rgba(71,65,98,.9)',
                    'borderColor' => 'rgba(71,65,98,.9)',
                ]
            ]
        ];
    }

    private function inventoryStatus($request)
    {

        $purchaseOrders = PurchaseOrder::selectRaw("
            COUNT(*) as totalPo,
            SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = '1' THEN total_amount ELSE 0 END) as totalAmount,
            SUM(CASE WHEN status = '1' THEN remaining_amount ELSE 0 END) as remaining
        ")
            ->when($request->from, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->to, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })
            ->first();

        $lowStock = ProductVariation::where('status', '0')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 20)
            ->count();

        $highStock = ProductVariation::where('status', '0')
            ->where('stock', '>', 150)
            ->count();

        $activeCategoryCount = Product::distinct('category_id')->count('category_id');

        $totalTags = Tag::count();

        return [
            'purchaseOrders' => [
                'totalPo' => $purchaseOrders->totalPo,
                'approved' => $purchaseOrders->approved,
                'pending' => $purchaseOrders->pending,
                'rejected' => $purchaseOrders->rejected,
                'totalAmount' => $purchaseOrders->totalAmount,
                'remaining' => $purchaseOrders->remaining,
                'paid' => $purchaseOrders->totalAmount - $purchaseOrders->remaining,
            ],

            'lowStock' => $lowStock,
            'highStock' => $highStock,

            'categories' =>  $activeCategoryCount,
            'totalTags' => $totalTags,
        ];
    }

    private function revenueOrderGraph()
    {
        // Get the date range for the last 6 months
        $startDate = now()->subMonths(6)->startOfMonth();
        $endDate = now()->endOfMonth();

        // Filter orders based on type and status with specific rules
        $filteredData = Order::where(function ($query) {
                $query->where('type', 'Normal')->where('status', '8'); // Include Normal with status 8
            })
            ->orWhere(function ($query) {
                $query->whereIn('type', ['Daraz', 'Cash'])->where('status', '5'); // Include Daraz and Cash with status 5
            })
            ->whereBetween('created_at', [$startDate, $endDate]) // Filter for the last 6 months
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at->format('Y-m'); // Group by year-month
            });

        // Prepare data for the chart
        $data = $filteredData->map(function ($monthOrders, $month) {
            return [
                'month' => date('M-y', strtotime($month)),
                'low' => $monthOrders->count(), // Count of orders (low data)
                'high' => $monthOrders->sum('total_bill'), // Sum of total_bill (high data)
            ];
        })->values();

        return [
            'categories' => $data->pluck('month'), // X-axis labels
            'series' => [
                [
                    'name' => 'Order Count',
                    'data' => $data->pluck('low'),
                ],
                [
                    'name' => 'Revenue',
                    'data' => $data->pluck('high'),
                ],
            ]];
    }

    private function dropshipperGraph()
    {
        // Get the last 12 months with their names
        $months = collect(range(0, 11))->map(function ($i) {
            return [
                'month' => now()->subMonths($i)->format('Y-m'),
                'name' => now()->subMonths($i)->format('M'),
            ];
        })->reverse(); // Reverse to get chronological order

        // Fetch data for dropshippers with status 2 within the last 12 months
       $dropshippers = DropShipper::where('status', '1')
            ->where('created_at', '>=', now()->subYear())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month');

        // Map dropshipper data to each month
        $data = $months->map(function ($month) use ($dropshippers) {
            return $dropshippers->get($month['month'], 0); // Default to 0 if no data
        });

        return [
            'categories' => $months->pluck('name'), // Extract month names
            'series' => [
                [
                    'name' => 'Dropshippers Registered',
                    'data' => [...$data],
                ],
            ],
        ];
    }


    private function topFiveSellingProduct($request)
    {

        $saleKinds = ['Normal' => 8, 'Cash' => 5, 'Daraz' => 5]; // Map sale kinds to their statuses
        $allOrders = collect();

        foreach ($saleKinds as $type => $status) {
            $orders = Order::where('type', $type)->where('status', $status)
                ->when($request->from, function ($q) use ($request) {
                    $q->whereDate('created_at', '>=', $request->from);
                })
                ->when($request->to, function ($q) use ($request) {
                    $q->whereDate('created_at', '<=', $request->to);
                })
                ->pluck('id');
            $allOrders = $allOrders->merge($orders);
        }

        $topFiveSellingProducts = OrderItem::with('variation.product')
            ->whereIn('order_id', $allOrders)
            ->select(
                'product_variation_id',
                DB::raw('SUM(price * quantity) as selling_price'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('product_variation_id')
            ->orderByDesc('total_quantity')
            ->limit(5) // Limit to the top 5 selling products
            ->get();

        return $topFiveSellingProducts;
    }

    private function topFiveDropshippers($request)
    {

        $topDropshippers = DropShipper::orderBy('total_payable', 'desc')->limit(5)->get();

        return $topDropshippers;
    }

    private function topFiveSuppliers($request)
    {

        $topFiveSuppliers = PurchaseOrder::with('supplier')
            ->select(
                'supplier_id',
                DB::raw('SUM(total_amount) as total_amount_sum'),
                DB::raw('SUM(remaining_amount) as remaining_amount_sum')
            )
            ->when($request->from, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->to, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })
            ->where('status', '1')
            ->groupBy('supplier_id')
            ->orderByDesc('total_amount_sum') // Order by the sum of total amounts
            ->limit(5) // Limit to top 5 suppliers
            ->get();


        return $topFiveSuppliers;
    }

    private function orderProcessed($orders)
    {
        $processed = $orders->whereNotIn('status', ['6', '7'])->where('type', 'Normal')->count();
        $returns = $orders->whereIn('status', ['9', '10'])->count();
        $inProcess = $orders->where('type', 'Normal')->whereNotIn('status', [6, 7, 8, 9, 10])->count();

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

        $returnRatio = $processed > 0 ? ($returns / ($processed -  $inProcess)) * 100 : 0;

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

    private function allProcessedOrder($orders, $request)
    {
        $from = $request->from;
        $to = $request->to;

        // Current Period
        $overallProcessed = $orders->whereNotIn('status', ['6', '7'])->count();

        $overallProcessedAmount = $orders->whereNotIn('status', ['6', '7'])->sum('total_bill');

        // Previous Period
        $previousFrom = Carbon::parse($from)->subDays(Carbon::parse($from)->diffInDays($to))->toDateString();
        $previousTo = Carbon::parse($to)->subDays(Carbon::parse($from)->diffInDays($to))->toDateString();

        $overallPreviousProcessed = Order::whereNotIn('status', ['6', '7'])
            ->whereDate('created_at', '>=', $previousFrom)
            ->whereDate('created_at', '<=', $previousTo)
            ->count();

        // Calculate Changes
        $overallProcessedDifference = $overallProcessed - $overallPreviousProcessed;
        $overallProcessedTrend = $overallProcessedDifference > 0 ? 'Increase' : ($overallProcessedDifference < 0 ? 'Decrease' : 'No Change');
        $overallProcessedPercentage = $overallPreviousProcessed > 0 ? ($overallProcessedDifference / $overallPreviousProcessed) * 100 : null;

        // Define order types
        $orderTypes = ['Normal', 'Cash', 'Daraz'];

        $data = [];

        foreach ($orderTypes as $type) {
            // Current Period
            $processed = $orders->where('type', $type)
                ->whereNotIn('status', ['6', '7'])
                ->count();

            $processedAmount = $orders->where('type', $type)
                ->whereNotIn('status', ['6', '7'])
                ->sum('total_bill');

            // Previous Period
            $previousFrom = Carbon::parse($from)->subDays(Carbon::parse($from)->diffInDays($to))->toDateString();
            $previousTo = Carbon::parse($to)->subDays(Carbon::parse($from)->diffInDays($to))->toDateString();

            $previousProcessed = Order::where('type', $type)
                ->whereNotIn('status', ['6', '7'])
                ->whereDate('created_at', '>=', $previousFrom)
                ->whereDate('created_at', '<=', $previousTo)
                ->count();

            // Calculate Changes
            $processedDifference = $processed - $previousProcessed;
            $processedTrend = $processedDifference > 0 ? 'Increase' : ($processedDifference < 0 ? 'Decrease' : 'No Change');
            $processedPercentage = $previousProcessed > 0 ? ($processedDifference / $previousProcessed) * 100 : null;

            $data[$type] = [
                'current' => $processed,
                'previous' => $previousProcessed,
                'difference' => $processedDifference,
                'trend' => $processedTrend,
                'percentage_change' => round(abs($processedPercentage)),
                'processedAmount' => $processedAmount,
            ];
        }

        return [
            'processed' => [
                'current' => $overallProcessed,
                'previous' => $overallPreviousProcessed,
                'difference' => $overallProcessedDifference,
                'trend'      => $overallProcessedTrend,
                'percentage_change' => round(abs($overallProcessedPercentage)),
                'processedAmount' => $overallProcessedAmount
            ],
            'other' => $data
        ];
    }


    private function pendingPayouts()
    {
        $dropshipper = DropShipper::with('general_ledger.dropshipper_shop_ledger.dropshipper_last_paid_voucher')->whereColumn('total_payable', '!=', 'total_paid')->get();

        $totalPayable = DropShipper::sum('total_payable');
        $totalPayablePaid = DropShipper::sum('total_paid');
        $totalRemaining   = DropShipper::sum('remaining_amount');
        $remainingDropshippers = $dropshipper->count();

        return  [
            'total_payable' => $totalPayable,
            'total_paid' => $totalPayablePaid,
            'total_remaining' => $totalRemaining,
            'remaining_dropshippers' => $remainingDropshippers,
        ];
    }

    private function pendingRequests($request)
    {
        $from = $request->from;
        $to = $request->to;

        $tickets = Ticket::where('status', '!=', 'Closed')
            ->when($from, function ($q) use ($from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('created_at', '<=', $to);
            })
            ->count();

        $pendingDropshippers = DropShipper::where('status', '0')
            ->when($from, function ($q) use ($from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('created_at', '<=', $to);
            })
            ->count();

        $pendingSuppliers = Supplier::where('status', '0')
            ->when($from, function ($q) use ($from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('created_at', '<=', $to);
            })
            ->count();

        $pendingReceivable = Order::where('status', '9')
            ->when($from, function ($q) use ($from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('created_at', '<=', $to);
            })
            ->count();


        $pendingPO = PurchaseOrder::where('status', '0')
            ->when($from, function ($q) use ($from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($q) use ($to) {
                $q->whereDate('created_at', '<=', $to);
            })
            ->count();

        $supplier = [
            'total'    => Supplier::count(),
            'approved' => Supplier::where('status', '1')->count(),
            'reject' => Supplier::where('status', '2')->count(),
            'pending'  => Supplier::where('status', '0')->count(),
        ];


        $dropshipper = [
            'total'    => DropShipper::count(),
            'approved' => DropShipper::where('status', '1')->count(),
            'reject' => DropShipper::where('status', '2')->count(),
            'pending'  => DropShipper::where('status', '0')->count(),
        ];

        return  [
            'tickets'             => $tickets,
            'pendingDropshippers' => $pendingDropshippers,
            'pendingSuppliers'    => $pendingSuppliers,
            'pendingReceivable'   => $pendingReceivable,
            'pendingPO'           => $pendingPO,
            'supplier'           => $supplier,
            'dropshippers'        => $dropshipper
        ];
    }

    private function getLiveProducts()
    {

        $products = Product::where('status', '0')->pluck('id');

        $variations = ProductVariation::whereIn('product_id', $products)
            ->where('status', '0')
            ->where('stock', '>', '0')->get(['id', 'stock', 'avg_price']);

        $currentStockValue = 0;
        foreach ($variations as $variation) {
            $currentStockValue += (float)$variation->stock * (float)$variation->avg_price;
        }

        return [
            'count'             => $products->count(),
            'currentStockValue' => $currentStockValue
        ];
    }

    private function getApprovedDropshippers($request)
    {
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

    private function getActiveSeller($request)
    {
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

    public function categoryTagWiseProduct()
    {

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

    public function shopListForPostEx(){
        $shops = DropShipperShop::with('dropshipper')->where('postex_store_code', '!=', '0')->get();
        foreach( $shops as $shop ){
            $currenyShipperCode = substr($shop->dropshipper->full_name, 0, 3) . '-' . substr($shop->store_name, 0, 3) . '-' . $shop->dropshipper->id;
            $requiredShipperCode = substr($shop->dropshipper->full_name, 0, 3) . '-' . substr($shop->store_name, 0, 3) . '-' . $shop->id;
            $storeCode = substr($shop->store_name, 0, 3) . '-' . $shop->id;

            $shop->current = $currenyShipperCode;
            $shop->required = $requiredShipperCode;
            $shop->shop_id = $storeCode;
        }

         return (new ResponseCollection($shops))
            ->response()
            ->setStatusCode(200);
    }

    public function topSellingProduct()
    {

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

    public function topTenDropshipper(Request $request)
    {

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
            ->whereIn('status', ['8', '9', '10'])
            ->get();

        $data = [
            'dropshippers' => $dropshippers,

            'dropshipperPayouts' => [
                'total' => $orders->sum('total_profit'),
                'paid' => $orders->sum('total_paid_profit'),
                'remaining' => $orders->sum('total_profit') - $orders->sum('total_paid_profit'),
                'total_sellers' => DropShipper::when($request->from, function ($q) use ($request) {
                        $q->whereDate('created_at', '>=', $request->from);
                    })
                    ->when($request->to, function ($q) use ($request) {
                        $q->whereDate('created_at', '<=', $request->to);
                    })->select('status')->count()
            ],

            'dropshipperApplication' => DropShipper::when($request->from, function ($q) use ($request) {
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
