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

        $returns = StoreReturn::when($request->from, function ($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from);
        })
        ->when($request->to, function ($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to);
        })->pluck('order_id');

        $orders = Order::when($request->courier, function ($q) use ($request) {
            $q->where('courier_service_id', $request->courier);
        })
        ->where('type', 'Normal')
        ->where('status', '10')
        ->whereIn('id', $returns)
        ->pluck('id');

        $data = OrderItem::with('variation.product', 'order.shop')->whereIn('order_id', $orders)->get();

        $grouped = $data->groupBy('product_variation_id')->map(function ($items) {
            $product = $items->first()->variation->product->title ?? null;
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
}
