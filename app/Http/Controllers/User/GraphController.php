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
use App\Models\Inventory\PurchaseOrder\PurchaseOrder;
use App\Models\Inventory\Store\StoreIssuance;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReceivedDetail;
use App\Models\Inventory\Store\StoreReturn;
use App\Models\Inventory\Store\StoreReturnDetail;
use App\Http\Controllers\Helpers\NotificationHelper;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperLevel;
use App\Models\User\DropShipperShop;
use App\Models\User\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GraphController extends Controller
{
   
    public function getDashboardGraphs()
    {
        try {
            // Generate last 30 days
            $days = collect(range(0, 29))->map(function ($i) {
                return [
                    'date' => now()->subDays($i)->format('Y-m-d'),
                    'label' => now()->subDays($i)->format('M d'),
                ];
            })->reverse();
    
            $dates = [];
            foreach ($days as $day) {
                $currentDate = $day['date'];
    
                // Match exactly with your closingReport logic
                $orders = Order::whereNotIn('status', ['6', '7'])
                    ->whereDate('created_at', $currentDate)
                    ->get();
    
                // Sales count (matching closingReport)
                $salesCount = $orders->count();
                $salesAmount = $orders->sum('total_bill');
    
                // Returns count (matching closingReport)
                $postExReturns = StoreReturnDetail::with('srn.order')
                    ->whereDate('created_at', $currentDate)
                    ->whereHas('srn.order', function ($q) {
                        $q->where('courier_service_id', '2');
                    })
                    ->count();
    
                $leopardReturns = StoreReturnDetail::with('srn.order')
                    ->whereDate('created_at', $currentDate)
                    ->whereHas('srn.order', function ($q) {
                        $q->where('courier_service_id', '1');
                    })
                    ->count();
    
                $returnsCount = $postExReturns + $leopardReturns;
    
                // NEW: Calculate Order Issuance Profit (same as your Vue calculation)
                $orderIssuanceProfit = $this->calculateDailyOrderIssuanceProfit($currentDate);
    
                $dates[$currentDate] = [
                    'sales_count' => $salesCount,
                    'sales_amount' => $salesAmount,
                    'returns_count' => $returnsCount,
                    'profit' => $orderIssuanceProfit // Use order issuance profit instead
                ];
            }
    
            // Build datasets (rest remains the same)
            $ordersData = $days->map(function ($day) use ($dates) {
                return (int) ($dates[$day['date']]['sales_count'] ?? 0);
            });
    
            $salesData = $days->map(function ($day) use ($dates) {
                return (int) ($dates[$day['date']]['sales_amount'] ?? 0);
            });
    
            $profitData = $days->map(function ($day) use ($dates) {
                return (float) ($dates[$day['date']]['profit'] ?? 0);
            });
    
            $returnsData = $days->map(function ($day) use ($dates) {
                return (int) ($dates[$day['date']]['returns_count'] ?? 0);
            });
    
            // Calculate statistics
            $stats = [
                'orders' => [
                    'total' => $ordersData->sum(),
                    'average' => round($ordersData->avg(), 1),
                ],
                'sales' => [
                    'total' => $salesData->sum(),
                    'average' => round($salesData->avg(), 1),
                ],
                'profit' => [
                    'total' => $profitData->sum(),
                    'average' => round($profitData->avg(), 1),
                ],
                'returns' => [
                    'total' => $returnsData->sum(),
                    'average' => round($returnsData->avg(), 1),
                ],
            ];
    
            return [
                'categories' => $days->pluck('label')->toArray(),
                'datasets' => [
                    'orders' => [
                        'name' => 'Daily Orders',
                        'data' => $ordersData->values()->toArray(),
                        'color' => '#7367F0',
                        'stats' => $stats['orders'],
                    ],
                    'sales' => [
                        'name' => 'Daily Sales',
                        'data' => $salesData->values()->toArray(),
                        'color' => '#28C76F',
                        'stats' => $stats['sales'],
                    ],
                    'profit' => [
                        'name' => 'Daily Profit',
                        'data' => $profitData->values()->toArray(),
                        'color' => '#00E396',
                        'stats' => $stats['profit'],
                    ],
                    'returns' => [
                        'name' => 'Daily Returns',
                        'data' => $returnsData->values()->toArray(),
                        'color' => '#EA5455',
                        'stats' => $stats['returns'],
                    ],
                ],
            ];
    
        } catch (\Exception $e) {
            \Log::error('Dashboard Graphs Error: ' . $e->getMessage());
            return $this->getSampleData();
        }
    }
    
    // NEW METHOD: Same calculation as your Vue component
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
    
            // Calculate profit (SAME AS YOUR VUE CALCULATION)
            $netQuantity = $quantity - $returnQuantity;
            $netSale = $avgIssuancePrice * $netQuantity;
            $netPurchase = $netQuantity * $purchaseRate;
            $profit = $netSale - $netPurchase;
    
            $totalProfit += $profit;
        }
    
        return $totalProfit;
    }

    public function newproducts30daysgraph()
    {
        // Generate last 30 days
        $days = collect(range(0, 29))->map(function ($i) {
            return [
                'date' => now()->subDays($i)->format('Y-m-d'),
                'label' => now()->subDays($i)->format('M d'),
            ];
        })->reverse();
        $products = Product::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');
        $data = $days->map(function ($day) use ($products) {
            return $products->get($day['date'], 0);
        });
        $stats = [
            'total' => $data->sum(),
            'average' => round($data->avg(), 1),
        ];
    
        return [
            'categories' => $days->pluck('label')->toArray(),
            'series' => [
                [
                    'name' => 'New Products',
                    'data' => [...$data],
                    'stats' => $stats,
                ],
            ],
        ];
    }

    public function dropshipperGraphLast120Days()

    {
    
     $days = collect(range(0, 29))->map(function ($i) {
    
        return [
    
            'date' => now()->subDays($i)->format('Y-m-d'),
    
            'label' => now()->subDays($i)->format('M d'),
    
        ];
    
    })->reverse(); 
    
    $dropshippers = DropShipper::where('status', '1')
    
        ->where('created_at', '>=', now()->subDays(30))
    
        ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
    
        ->groupBy('date')
    
        ->pluck('count', 'date');
    
    $data = $days->map(function ($day) use ($dropshippers) {
    
        return $dropshippers->get($day['date'], 0);
    
    });
    
    
    $stats = [
        'total' => $data->sum(),
        'average' => round($data->avg(), 1),
    ];
    return [
    
        'categories' => $days->pluck('label'), 
    
        'series' => [
    
            [
    
                'name' => 'Approved Dropshippers (Last 30 Days)',
    
                'data' => [...$data],
                'stats' => $stats,
    
            ],
    
        ],
    
    ];
    }
    
    public function ticketTypesGraphData()
    {
        // Get ticket counts by type for last 30 days, only where count > 0
        $ticketTypes = Ticket::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('ticket_type, COUNT(*) as count')
            ->groupBy('ticket_type')
            ->having('count', '>', 0)
            ->orderBy('count', 'DESC')
            ->get();
    
        // If no tickets found, return sample data for demonstration
        if ($ticketTypes->isEmpty()) {
            return [
                'categories' => [
                    'Order Issues',
                    'Payment Issues', 
                    'Account Re-Activation',
                    'Product Stock/Information',
                    'Shipping Issues',
                    'Invoice Issues'
                ],
                'series' => [
                    [
                        'name' => 'Ticket Types',
                        'data' => [15, 12, 8, 6, 5, 4],
                        'stats' => [
                            'total' => 50,
                            'average' => 8.3
                        ]
                    ]
                ]
            ];
        }
    
        $totalTickets = $ticketTypes->sum('count');
    
        // Format for ApexCharts
        $categories = $ticketTypes->pluck('ticket_type');
        $seriesData = $ticketTypes->pluck('count');
    
        return [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Ticket Types',
                    'data' => $seriesData,
                
                ]
            ]
        ];
    }
    
}