<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\PurchaseOrder\PurchaseOrder;
use App\Models\Inventory\PurchaseOrder\PurchaseOrderDetail;
use App\Models\Inventory\Store\StoreIssuance;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReceivedDetail;
use App\Models\Inventory\Store\StoreReturn;
use App\Models\Inventory\Store\StoreReturnDetail;
use App\Models\Ticket;
use App\Models\User\DropShipper;
use App\Models\User\SupplierStock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FisReportController extends Controller
{
    public function index()
    {
        return view('report.fis');
    }

    public function inventoryControlRegister(Request $request)
    {

        $data = Product::with([
            'variation:id,product_id,avg_price,sku',
            'opening_stock' => function ($q) use ($request) {
                $q->whereDate('created_at', '<', $request->from)
                    ->select(
                        DB::raw("sum(total) as total"),
                        DB::raw("sum(total) / sum(quantity) as rate"),
                        DB::raw("sum(quantity) as quantity"),
                        'product_id'
                    )
                    ->groupBy('product_id');
            },
            'opening_issuance' => function ($q) use ($request) {
                $q->whereDate('created_at', '<', $request->from)
                    ->select(
                        DB::raw("sum(total) as total"),
                        DB::raw("sum(total) / sum(quantity) as rate"),
                        DB::raw("sum(quantity) as quantity"),
                        'product_id'
                    )
                    ->groupBy('product_id');
            },
            'opening_returns' => function ($q) use ($request) {
                $q->whereDate('created_at', '<', $request->from)
                    ->select(
                        DB::raw("sum(total) as total"),
                        DB::raw("sum(total) / sum(quantity) as rate"),
                        DB::raw("sum(quantity) as quantity"),
                        'product_id'
                    )
                    ->groupBy('product_id');
            },
            'good_receive' => function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from)
                    ->whereDate('created_at', '<=', $request->to)
                    ->select(
                        DB::raw("sum(total) as total"),
                        DB::raw("sum(total) / sum(quantity) as rate"),
                        DB::raw("sum(quantity) as quantity"),
                        'product_id'
                    )
                    ->groupBy('product_id');
            },
            'return' => function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from)
                    ->whereDate('created_at', '<=', $request->to)
                    ->select(
                        DB::raw("sum(quantity) as quantity"),
                        'product_id'
                    )
                    ->groupBy('product_id');
            },
            'issuance' => function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from)
                    ->whereDate('created_at', '<=', $request->to)
                    ->select(
                        DB::raw("sum(total) as total"),
                        DB::raw("sum(total) / sum(quantity) as rate"),
                        DB::raw("sum(quantity) as quantity"),
                        'product_id'
                    )
                    ->groupBy('product_id');
            }
        ])->get();


        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function goodReceivedRegister(Request $request)
    {
        $goods = StoreReceivedDetail::with('product', 'grn.purchase_order.supplier')
            ->when($request->from, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->to, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })
            ->when($request->po, function ($q) use ($request) {
                $q->whereHas('grn', function ($query) use ($request) {
                    $query->where('po_id', $request->po);
                });
            })
            ->when($request->product['code'], function ($q) use ($request) {
                $q->whereHas('product', function ($query) use ($request) {
                    $query->where('id', $request->product['code']);
                });
            })
            ->get();

        $data = [
            'goods' => $goods,
            'role'  => auth()->user()->role
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function goodReceivedDelete(Request $request)
    {
        //First case check PO amount
       $total = $request->total;
        $po = PurchaseOrder::where('id', $request->grn['po_id'])->first();
        if ($po) {
            if ($total > $po->remaining_amount) {
                return (new ValidationCollection(['Total amount exceeds the remaining amount in the purchase order.']))
                    ->response()
                    ->setStatusCode(422);
            }

             //If GRN is created from adjustment module then direct delete and update average price
            //Total average rate = ( average_rate * stock ) + (new_qty * new_rate) / total_stock + new_qty
            // Calculate new average price after deletion
            $product = ProductVariation::where('product_id', $request->product_id)->first();
            $grns = StoreReceivedDetail::where('product_id', $request->product_id)->get();
            // Calculate new average price after deletion
            $current_total_quantity = $grns->sum('quantity');
            $current_total_cost = $grns->sum(function ($grn) {
                    return $grn->total;
                });

            // Stock to delete
            $delete_quantity = $request->quantity;
            $delete_rate = $request->price;

            // Adjust total quantity
            $new_total_quantity = $current_total_quantity - $delete_quantity;

            // Adjust total cost by removing deleted stock's cost
            $deleted_stock_cost = $delete_rate * $delete_quantity;
            $adjusted_total_cost = $current_total_cost - $deleted_stock_cost;

            // Calculate new average rate
            $new_average_rate = $adjusted_total_cost / $new_total_quantity;

            // Round and return new average rate
            $product->update([
                'avg_price' => round($new_average_rate)
            ]);

            $product->decrement('stock', $delete_quantity);

            StoreReceivedDetail::where('id', $request->id)->delete();
            PurchaseOrderDetail::where('po_id', $po->id)->delete();
            $po->delete();
            return ['message' => 'Successfully Deleted'];
        } else if ($request->grn['po_id'] == 0) {
            //If GRN is created from adjustment module then direct delete and update average price
            //Total average rate = ( average_rate * stock ) + (new_qty * new_rate) / total_stock + new_qty
            // Calculate new average price after deletion
            $product = ProductVariation::where('product_id', $request->product_id)->first();
            $grns = StoreReceivedDetail::where('product_id', $request->product_id)->get();
            // Calculate new average price after deletion
            $current_total_quantity = $grns->sum('quantity');
           $current_total_cost = $grns->sum(function ($grn) {
                return $grn->total;
            });

            // Stock to delete
            $delete_quantity = $request->quantity;
            $delete_rate = $request->price;

            // Adjust total quantity
            $new_total_quantity = $current_total_quantity - $delete_quantity;

            // Adjust total cost by removing deleted stock's cost
            $deleted_stock_cost = $delete_rate * $delete_quantity;
            $adjusted_total_cost = $current_total_cost - $deleted_stock_cost;

            // Calculate new average rate
            $new_average_rate = $adjusted_total_cost / $new_total_quantity;

            // Round and return new average rate
            $product->update([
                'avg_price' => round($new_average_rate)
            ]);

            $product->decrement('stock', $delete_quantity);

            StoreReceivedDetail::where('id', $request->id)->delete();
            return ['message' => 'Successfully Deleted'];
        }
    }

    public function goodIssuedRegister(Request $request)
    {
        $data = StoreIssuanceDetail::with('product', 'sin.order.shop')
            ->when($request->from, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->to, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })
            ->when($request->product['code'], function ($q) use ($request) {
                $q->whereHas('product', function ($query) use ($request) {
                    $query->where('id', $request->product['code']);
                });
            })
            ->get();

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }
    public function goodReturnRegister(Request $request)
    {
        $data = StoreReturnDetail::with('product', 'srn.order.shop')
            ->when($request->from, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->to, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })
            ->when($request->product['code'], function ($q) use ($request) {
                $q->whereHas('product', function ($query) use ($request) {
                    $query->where('id', $request->product['code']);
                });
            })
            ->get();

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function deliveredOrders(Request $request)
    {
        $orders = Order::when($request->from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from);
        })
        ->when($request->to, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to);
        })
        ->where('type', 'Normal')
        ->where('status', '8')
        ->pluck('id');

        $data = OrderItem::with('variation.product')->whereIn('order_id', $orders)->get();

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function leopardReturnsReceived(Request $request)
    {
        $data = StoreReturnDetail::with('product:id,title', 'srn.order')
            ->when( $request->from, function ($query, $from) {
                return $query->whereDate('created_at', '>=', $from);
            })
            ->when( $request->to, function ($query, $to) {
                return $query->whereDate('created_at', '<=', $to);
            })
            ->when($request->courier, function ($query, $courier) {
                return $query->whereHas('srn.order', function ($q) use ($courier) {
                    $q->where('courier_service_id', $courier);
                });
            })
        ->orderBy('id','desc')
        ->get();

        $grouped = $data->groupBy('product_id')->map(function ($items) {
            $product = $items->first()->product->title ?? null;
            return [
                'product_name' => $product ?? 'N/A',
                'total_qty'    => $items->sum('quantity'),
            ];

        })->values();

        return (new ResponseCollection($grouped))
            ->response()
            ->setStatusCode(200);
    }

    public function orderIssuance(Request $request)
    {

        $orders = StoreIssuance::when($request->from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from);
        })
        ->when($request->to, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to);
        })
        ->where('order_id', '!=','0')
        ->pluck('id');

        $issues = StoreIssuanceDetail::with('product.variation', 'sin')
        ->whereIn('sin_id', $orders)
        ->get()->groupBy('product_id');

        $products = [];
        foreach( $issues as $singleProductGroup ){

            $products[$singleProductGroup[0]->product_id]['sku'] = $singleProductGroup[0]->product->variation->sku;
            $products[$singleProductGroup[0]->product_id]['name'] = $singleProductGroup[0]->product->title;
            $quantity = $singleProductGroup->sum('quantity');
            $products[$singleProductGroup[0]->product_id]['quantity'] = $quantity;


            //Calculate Avg Purchase Price
            $rate = StoreReceivedDetail::where('created_at', '<=', $request->to)
            ->where('product_id', $singleProductGroup[0]->product_id)
            ->select(DB::raw("SUM(total) / SUM(quantity) as rate"))
            ->first();

            $purchase_cost = (float)$rate->rate * $quantity;
            $products[$singleProductGroup[0]->product_id]['purchase_rate'] = round($rate->rate);
            $products[$singleProductGroup[0]->product_id]['purchase_cost'] = round( $purchase_cost );

             //Calculate Avg Issuance Price
             $issance_price = $singleProductGroup->sum('total');
             $products[$singleProductGroup[0]->product_id]['issance_price'] = round( $issance_price / $quantity );
             $products[$singleProductGroup[0]->product_id]['issance_cost'] = round( $issance_price );

             //Get Return and calculate there total
             $return_quantity = 0;
             foreach($singleProductGroup as $order ){
                $order_no = $order->sin->order_id;
                $product = $order->product_id;

               $returned = StoreReturn::where('order_id', $order_no)->first();
                if( $returned ){
                    $return_record = StoreReturnDetail::where('product_id', $product)->where('srn_id', $returned->id)->first();
                    $return_quantity += $return_record ? $return_record->quantity : 0;
                }
            }
            $products[$singleProductGroup[0]->product_id]['returned'] = $return_quantity;
        }

        return (new ResponseCollection($products))
            ->response()
            ->setStatusCode(200);
    }

   public function closingReport(Request $request)
    {
        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);

        $dates = [];
        for ($date = $from; $date->lte($to); $date->addDay()) {
            $currentDate = $date->toDateString();

            $orders = Order::whereNotIn('status', ['6', '7'])
                ->whereDate('created_at', $currentDate)
                ->get();

            $postExOrders = $orders->where('courier_service_id', '2')->count();
            $leopardOrders = $orders->where('courier_service_id', '1')->count();
            $cashOrders = $orders->where('type', 'Cash')->count();
            $darazOrders = $orders->where('type', 'Daraz')->count();

            $leopardReturns = StoreReturnDetail::with('srn.order')
                ->whereDate('created_at', $currentDate)
                ->whereHas('srn.order', function ($q) {
                    $q->where('courier_service_id', '1');
                })
                ->count();

            $postExReturns = StoreReturnDetail::with('srn.order')
                ->whereDate('created_at', $currentDate)
                ->whereHas('srn.order', function ($q) {
                    $q->where('courier_service_id', '2');
                })
                ->count();

            $tickets = Ticket::whereDate('created_at', $currentDate)
                ->where(function ($q) {
                    $q->where('status', '!=', 'Closed')
                    ->orWhere('status', '=', 'Expired');
                })
                ->count();

            $dates[] = [
                'date' => $currentDate,
                'postEx' => $postExOrders,
                'leopards' => $leopardOrders,
                'cash' => $cashOrders,
                'daraz' => $darazOrders,
                'totalOrders' => $orders->count(),
                'totalSales' => $orders->sum('total_bill'),
                'postExReturns' => $postExReturns,
                'leopardReturns' => $leopardReturns,
                'totalReturns' => $postExReturns + $leopardReturns,
                'tickets' => $tickets
            ];
        }

        return (new ResponseCollection($dates))
            ->response()
            ->setStatusCode(200);
    }

    public function dropshipperList(){

        // Apply filters to the query
        $dropshippers = DropShipper::orderBy('id', 'desc')
            ->get();


        return (new ResponseCollection($dropshippers))
            ->response()
            ->setStatusCode(200);
    }
    public function suspectedDuplicateDropshippers()
    {
        // Get dropshippers with same name, email, or phone
        $duplicates = DropShipper::select('*')
            ->where('status', 1)
            ->whereIn('email', function($query) {
                $query->select('email')
                      ->from('drop_shippers')
                      ->where('status', 1)
                      ->whereNotNull('email')
                      ->where('email', '!=', '')
                      ->groupBy('email')
                      ->havingRaw('COUNT(*) > 1');
            })
            ->orWhereIn('whatsapp_number', function($query) {
                $query->select('whatsapp_number')
                      ->from('drop_shippers')
                      ->where('status', 1)
                      ->whereNotNull('whatsapp_number')
                      ->where('whatsapp_number', '!=', '')
                      ->groupBy('whatsapp_number')
                      ->havingRaw('COUNT(*) > 1');
            })
            ->orWhereIn('account_iban', function($query) {
                $query->select('account_iban')
                      ->from('drop_shippers')
                      ->where('status', 1)
                      ->whereNotNull('account_iban')
                      ->where('account_iban', '!=', '')
                      ->groupBy('account_iban')
                      ->havingRaw('COUNT(*) > 1');
            })
            ->orWhereIn('cnic_number', function($query) {
                $query->select('cnic_number')
                      ->from('drop_shippers')
                      ->where('status', 1)
                      ->whereNotNull('cnic_number')
                      ->where('cnic_number', '!=', '')
                      ->groupBy('cnic_number')
                      ->havingRaw('COUNT(*) > 1');
            })
            ->orWhereIn('account_number', function($query) {
                $query->select('account_number')
                      ->from('drop_shippers')
                      ->where('status', 1)
                      ->whereNotNull('account_number')
                      ->where('account_number', '!=', '')
                      ->groupBy('account_number')
                      ->havingRaw('COUNT(*) > 1');
            })
            ->orderBy('email')
            ->orderBy('whatsapp_number')
            ->orderBy('account_number')
            ->orderBy('cnic_number')
            ->get();
    
        return (new ResponseCollection($duplicates))
            ->response()
            ->setStatusCode(200);
    }

    public function supplierWiseStock(){

        // Apply filters to the query
        $dropshippers = SupplierStock::with('supplier:id,full_name', 'product:id,title')->orderBy('id', 'desc')
            ->get();


        return (new ResponseCollection($dropshippers))
            ->response()
            ->setStatusCode(200);
    }

    // In App\Http\Controllers\Report\FisReportController

// ... (existing methods)

/**
 * Calculates Low Stock Levels and Recommended Reorder Quantity based on sales data.
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function lowStockProducts(Request $request)
{
    try {
        $products = Product::with(['variation'])
            ->where('status', 1)
            ->get();

        $lowStockData = [];

        foreach ($products as $product) {
            if (!$product->variation) continue;

            // Get sales for last 30 days
            $sales30Days = StoreIssuanceDetail::where('product_id', $product->id)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->sum('quantity');

            // Fixed values
            $leadTime = 3;
            $desiredDays = 10;
            $currentStock = $product->variation->stock;
            
            // Average Daily Sales = Total units sold in the last 30 days ÷ 30
            $avgDailySales = $sales30Days / 30;
            
            // Safety Stock = (Maximum Daily Sales × Lead Time) − (Average Daily Sales × Lead Time)
            // Using 2x average as maximum for simplicity
            $maxDailySales = $avgDailySales * 2;
            $safetyStock = ($maxDailySales * $leadTime) - ($avgDailySales * $leadTime);
            $safetyStock = max(1, ceil($safetyStock)); // Minimum 1
            
            // Low Stock Level = (Average Daily Sales × Lead Time) + Safety Stock
            $lowStockLevel = ($avgDailySales * $leadTime) + $safetyStock;
            $lowStockLevel = ceil($lowStockLevel);
            
            // Determine status
            if ($currentStock <= 0) {
                $status = 'Out of Stock';
            } elseif ($currentStock <= $lowStockLevel) {
                $status = 'Low Stock';
            } else {
                $status = 'Sufficient';
            }
            
            // Recommended Reorder Quantity = (Average Daily Sales × 10) − Current Stock
            $reorderQty = 0;
            if ($status !== 'Sufficient') {
                $reorderQty = ($avgDailySales * $desiredDays) - $currentStock;
                $reorderQty = max(5, ceil($reorderQty)); // Minimum 5 units
            }

            $lowStockData[] = [
                'sku' => $product->variation->sku,
                'name' => $product->title,
                'current_stock' => (int) $currentStock,
                'sales_30_days' => (int) $sales30Days,
                'avg_daily_sales' => round($avgDailySales, 2),
                'lead_time' => $leadTime,
                'safety_stock' => (int) $safetyStock,
                'low_stock_level' => (int) $lowStockLevel,
                'status' => $status,
                'recommended_reorder_qty' => (int) $reorderQty,
                'last_updated' => $product->variation->updated_at->format('Y-m-d'),
            ];
        }

        // Simple status filter
        if ($request->status && $request->status !== 'all') {
            $lowStockData = array_values(array_filter($lowStockData, function($item) use ($request) {
                return $item['status'] === $request->status;
            }));
        }

        return (new ResponseCollection($lowStockData))
            ->response()
            ->setStatusCode(200);

    } catch (\Exception $e) {
        \Log::error('Low Stock Report Error: ' . $e->getMessage());
        return (new ResponseCollection([]))
            ->response()
            ->setStatusCode(500);
    }
}


}
