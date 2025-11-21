<?php

namespace App\Http\Controllers;

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

class GrowthController extends Controller
{

    public function index()
    {
        return view('growthdashboard');
    }

    public function fetchData(Request $request)
    {
        $orders = Order::when($request->from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from);
        })
        ->when($request->to, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to);
        })->get();
    
        $pendingPayouts     = $this->pendingPayouts();
        $pendingRequests    = $this->pendingRequests($request);
        $allProcessedOrders = $this->allProcessedOrder($orders, $request);
        $todaysData = $this->getTodaysData(); // Today's numbers
    
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
            'payOuts'            => $pendingPayouts,
            'pendingRequests'    => $pendingRequests,
            'todaysData'         => $todaysData, // Add today's data here
            'allProcessedOrders' => $allProcessedOrders,
            'courierPerformance' => $courierPerformance,
            'levels'             => $levels,
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
    
        return [
            'orders' => $orders->count(),
            'sales' => $orders->sum('total_bill'), // Same as orders count
            'profit' => $profit,
            'returns' => $returns
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
}


