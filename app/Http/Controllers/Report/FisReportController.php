<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReceivedDetail;
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
            'variation:id,product_id,avg_price',
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
        $data = StoreReceivedDetail::with('product', 'grn.purchase_order.supplier')
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

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
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
        $data = StoreReturnDetail::with('product', 'srn')
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
}
