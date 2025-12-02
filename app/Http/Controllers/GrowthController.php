<?php

namespace App\Http\Controllers;

use App\Services\ProfitService;
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
        
        $todaysData = $this->getTodaysData();

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
    
    $orders = Order::whereNotIn('status', ['6', '7'])
        ->whereDate('created_at', $today)
        ->get();
        
    $orderbelongsto = $orders->pluck('belongs_to')->unique()->values();
    $todaysActiveSellerIds = DropShipper::where('status', '1')
        ->whereIn('user_id', $orderbelongsto)
        ->count();

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
    
}