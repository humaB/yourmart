<?php

namespace App\Services;

use App\Models\Inventory\Store\StoreIssuance;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReceivedDetail;
use App\Models\Inventory\Store\StoreReturn;
use App\Models\Inventory\Store\StoreReturnDetail;
use Illuminate\Support\Facades\DB;

class ProfitService
{

    public static function calculateDailyOrderIssuanceProfit($date)
{
    $orders = StoreIssuance::whereDate('created_at', $date)
        ->where('order_id', '!=', '0')
        ->pluck('id');

    $issues = StoreIssuanceDetail::with('product.variation', 'sin')
        ->whereIn('sin_id', $orders)
        ->get()
        ->groupBy('product_id');

    $totalProfit = 0;

    foreach ($issues as $group) {

        $quantity = $group->sum('quantity');
        $productId = $group[0]->product_id;

        $purchase = StoreReceivedDetail::where('created_at', '<=', $date)
            ->where('product_id', $productId)
            ->select(DB::raw("SUM(total) / SUM(quantity) as rate"))
            ->first();

        $purchaseRate = round($purchase->rate ?? 0);

        $totalIssuance = $group->sum('total');
        $avgIssuancePrice = $quantity > 0 ? round($totalIssuance / $quantity) : 0;
        $returnedQty = 0;
        foreach ($group as $item) {
            $orderNo = $item->sin->order_id;

            $returned = StoreReturn::where('order_id', $orderNo)->first();
            if ($returned) {
                $r = StoreReturnDetail::where('product_id', $productId)
                    ->where('srn_id', $returned->id)
                    ->first();
                $returnedQty += $r ? $r->quantity : 0;
            }
        }

        $netQuantity = $quantity - $returnedQty;

        $netSale = $netQuantity * $avgIssuancePrice;
        $netPurchase = $netQuantity * $purchaseRate;

        $profit = $netSale - $netPurchase;

        $totalProfit += $profit;
    }

    return (int) $totalProfit;
}

}

?>