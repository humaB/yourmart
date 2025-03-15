<?php

namespace App\Http\Controllers\Inventory\Store;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\ProductQrCode;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Inventory\PurchaseOrder\PurchaseOrder;
use App\Models\Inventory\PurchaseOrder\PurchaseOrderDetail;
use App\Models\Inventory\Store\StoreIssuance;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\Inventory\Store\StoreReceived;
use App\Models\Inventory\Store\StoreReceivedDetail;
use App\Models\Inventory\Store\StoreReturnDetail;
use Exception;
use Illuminate\Support\Facades\DB;
use TCPDF;
include(public_path().'/assets/tcpdf/tcpdf.php');

class StoreInwardController extends Controller
{
    public function index()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'inventory manager'){
            abort(401);
        }
        return view('inventory.store.store_pending_purchase_order');
    }

    public function record()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'inventory manager' && auth()->user()->role != 'supervisor'){
            abort(401);
        }
        return view('inventory.store.store_inward_record');
    }

    public function stock()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'inventory manager' && auth()->user()->role != 'supervisor'){
            abort(401);
        }
        return view('inventory.store.stock');
    }

    public function fetchStock(){

        $stock = ProductVariation::with('product' , 'barcode')->where('status', '0')->where('stock', '!=', 0 )->get();
        $totalProducts = ProductVariation::where('status', '0')->count();
        $totalQuantity = ProductVariation::where('status', '0')->sum('stock');

        $lowStock = ProductVariation::where('status', '0')
        ->where('stock', '>', 0)
        ->where('stock', '<=', 20)
        ->count();

        $highStock = ProductVariation::where('status', '0')
        ->where('stock', '>', 150)
        ->count();

        $outOfStock =  ProductVariation::where('status', '0')
        ->where('stock', '<=', 0)
        ->count();

        // Step 1: Get IDs of product variations with status 0
        $withOutBarcodeVariationIds = ProductVariation::where('status', '0')
        ->pluck('id');

        // Step 2: Get IDs of product variations that have a barcode of '-'
        $withBarcodeIds = ProductQrCode::whereIn('product_variation_id', $withOutBarcodeVariationIds)
        ->where('barcode', '-')
        ->count();

        $data = [
            'stock'         => $stock,
            'totalProducts' => $totalProducts,
            'totalQuantity' => $totalQuantity,
            'lowStock'      => $lowStock,
            'highStock'     => $highStock,
            'outOfStock'   => $outOfStock,
            'withoutBarcodeCount' => $withBarcodeIds
        ];

        return (new ResponseCollection($data))
        ->response()
        ->setStatusCode(200);
    }

    public function filterStock( Request $request ){

        if( $request->filter == 'low_stock'){
            $data = ProductVariation::with('product' , 'barcode')->where('status', '0')
            ->where('stock', '>', 0)
            ->where('stock', '<=', 20)
            ->get();
        }

        if( $request->filter == 'high_stock'){
            $data = ProductVariation::with('product' , 'barcode')->where('status', '0')
            ->where('stock', '>', 150)
            ->get();
        }

        if( $request->filter == 'out_of_stock'){
            $data =  ProductVariation::with('product' , 'barcode')->where('status', '0')
            ->where('stock', '<=', 0)
            ->get();
        }

        return (new ResponseCollection($data))
        ->response()
        ->setStatusCode(200);
    }

    public function adjustStock( Request $request ){
        if(auth()->user()->role != 'admin'){
            return response()->json([
                'message' => 'Unauthorized access'
            ], 401);
        }

        // Check if increments are available in the request
        if ($request->has('increments') && is_array($request->increments)) {

            $grn = StoreReceived::create([
                'po_id'    => 0,
                'added_by' => auth()->user()->id,
            ]);

            foreach ($request->increments as $increment) {
                // Process each increment item
                // Example: Update inventory or perform desired actions
                $product = Product::find($increment['id']);
                if ($product) {
                    $variation = ProductVariation::where('product_id', $increment['id'])->first();
                    $variation->increment('stock', $increment['quantity']);

                    StoreReceivedDetail::create([
                        'grn_id'     => $grn->id,
                        'product_id' => $increment['id'],
                        'quantity'   => $increment['quantity'],
                        'price'      => $variation->avg_price,
                        'tax'        => 0,
                        'delivery_charges' => 0,
                        'discount'   => 0,
                        'total'      => (float)$increment['quantity'] * (float)$variation->avg_price,
                        'remarks'    =>  $increment['remark'] ?? '',
                        'added_by' => auth()->user()->id,
                    ]);
                }
            }
        }

        // Check if decrements are available in the request
        if ($request->has('decrements') && is_array($request->decrements)) {

            $issuance = StoreIssuance::create([
                'order_id'  => 0,
                'added_by'  => auth()->user()->id,
            ]);

            foreach ($request->decrements as $decrement) {
                // Process each decrement item
                // Example: Update inventory or perform desired actions
                $product = Product::find($decrement['id']);
                if ($product) {
                    $variation = ProductVariation::where('product_id', $decrement['id'])->first();
                    $variation->decrement('stock', $decrement['quantity']);

                    StoreIssuanceDetail::create([
                        'sin_id'     => $issuance->id,
                        'product_id' => $decrement['id'],
                        'quantity'   => $decrement['quantity'],
                        'price'      => $variation->avg_price,
                        'total'      => (float)$decrement['quantity'] * (float)$variation->avg_price,
                        'remarks'    =>  $decrement['remark'] ?? '',
                        'added_by' => auth()->user()->id,
                    ]);
                }
            }
        }

        // Return a response indicating the operation was successful
        return response()->json(['message' => 'Inventory updated successfully'], 200);
    }

    public function updateBarcode( Request $request ){

        ProductQrCode::updateOrCreate(
            [
                'product_variation_id' => $request->id,
            ],
            [
                'barcode' => $request->barcode ?? '-',
            ]
        );
        return ['message' => 'Successfully Updated'];
    }

    public function pendingPO(){

        $data = PurchaseOrderDetail::whereColumn('quantity', '!=', 'store_received_quantity')
        ->pluck('po_id');

        $data = PurchaseOrder::whereIn('id', $data)
        ->with('supplier:id,full_name', 'details.product')
        ->where('status', '1')
        ->orderBy('id','desc')
        ->get();

        return (new ResponseCollection($data))
        ->response()
        ->setStatusCode(200);
    }

    public function inwardRecord(){

        $data = StoreReceivedDetail::with('product', 'grn')
        ->orderBy('id','desc')
        ->get();

        return (new ResponseCollection($data))
        ->response()
        ->setStatusCode(200);
    }

    public function inWard( Request $request ){

        $lock = Cache::lock('store_inward')->block(7, function () use ($request) {
            DB::beginTransaction();

            try {
                $products = json_decode($request['details']);

                $grn = StoreReceived::create([
                    'po_id'     => $request['po'],
                    'added_by'  => auth()->user()->id,
                ]);

                $po = PurchaseOrder::where('id', $request['po'])->first();

                foreach ($products as $product) {
                    if ($product) {
                        $data = PurchaseOrderDetail::where('id', $product->id)->first();
                        if ($data->gate_received_quantity < ($data->store_received_quantity + $product->qty)) {
                            DB::rollBack();
                            return (new ValidationCollection(['Quantity added should be less than Receiveable']))
                                ->response()
                                ->setStatusCode(409);
                        }
                    }
                }

                foreach ($products as $product) {
                    if ($product) {
                        $data = PurchaseOrderDetail::where('id', $product->id)->first();
                        if ($data->gate_received_quantity >= $data->store_received_quantity + $product->qty) {
                            $receivedQty = $product->qty;
                            if( $receivedQty > 0 ){

                                $variation = ProductVariation::where('id', $data->product_variation_id)->first();

                                // Proportion of tax and discount based on total price
                                $taxForReceivedQty  = ($data->tax /  $data->quantity) * $receivedQty;
                                $discountForReceivedQty = ($data->discount /  $data->quantity) * $receivedQty;
                                $deliveryChargesForReceivedQty  = ($data->delivery_charges /  $data->quantity) * $receivedQty;

                                PurchaseOrderDetail::where('id', $product->id)->increment('store_received_quantity', $receivedQty);

                                StoreReceivedDetail::create([
                                    'grn_id' => $grn->id,
                                    'product_id' => $data->product_id,
                                    'quantity' => $receivedQty,
                                    'price' => $data->price,
                                    'tax' => round($taxForReceivedQty),
                                    'delivery_charges' => round($deliveryChargesForReceivedQty),
                                    'discount' => round($discountForReceivedQty),
                                    'total' => round(($data->price * $receivedQty) - $discountForReceivedQty + ($taxForReceivedQty + $deliveryChargesForReceivedQty)),
                                    'added_by' => auth()->user()->id,
                                ]);

                                // Calculate Weighted Average Rate
                                $totalCost = ($data->price * $receivedQty) - $discountForReceivedQty + ($taxForReceivedQty + $deliveryChargesForReceivedQty);
                                $avg_price = (((float) $variation->avg_price * (float) $variation->stock) + $totalCost) / ((float) $variation->stock + (float) $receivedQty);

                                $variation->increment('stock', $receivedQty);
                                $variation->update(['avg_price' => round($avg_price)]);

                                if (isset($product->qrCodeDataUrl) && $product->qrCodeDataUrl) {
                                    ProductQrCode::updateOrCreate(
                                        [
                                            'product_variation_id' => $data->product_variation_id,
                                        ],
                                        [
                                            'barcode' => $product->qrCodeDataUrl ?? '-',
                                        ]
                                    );
                                }
                            }
                        }
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => $e->getMessage()], 500);
            }

        });

        return $lock;
    }

    public function pdf( Request $request ){

        $details = StoreReceived::with('details.product')
        ->where('id', $request->id)
        ->first();

         $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
         $pdf->SetCreator(PDF_CREATOR);
         $pdf->SetAuthor('');
         $pdf->SetTitle('GRN Details');
         $pdf->SetSubject(' ');
         $pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
         $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
         $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
         $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
         $pdf->SetMargins(5, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
         $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
         $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
         $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
         // set default font subsetting mode
         $pdf->setFontSubsetting(true);
         $pdf->SetFont('times', 'B', 12, 'C', true);

         $pdf->AddPage();


         $pdf->SetFont('dejavusans', '', 10, 'C', true);

         $pdf->Ln(5);

         $pdf->SetFont('dejavusans', '', 10, 'C', true);
         $pdf->MultiCell(40, 0, "GRN Date ", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, date('d-M-Y', strtotime($details->created_at)), 1, 'R', 0, 0);



        $pdf->Ln();
         $pdf->MultiCell(40, 0, "GRN #", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, $details->id, 1, 'R', 0, 0);
         $pdf->MultiCell(15, 0, "", 0, 'C', 0, 0);

         $pdf->Ln(10);
         $pdf->SetFont('dejavusans', 'B', 8);
         $pdf->Cell(5, 0, "Sr", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(100, 0, "Product Name", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(80, 0, "Quantity", 1, 1, 'L', 0, '', 0, false, 'T',);

         $pdf->SetFont('dejavusans', '', 7.7);
         $total_quantity = 0;
         foreach($details->details as $index => $product ){
             $total_quantity += (float)$product->quantity;
             $pdf->Cell(5, 9, $index + 1, 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->MultiCell(100, 9,$product->product->title ?? '', 1, 'L', 0, 0, '', '', true, 0, false, true, 9, 'M');
             $pdf->Cell(80, 9, $product->quantity, 1, 1, 'L', 0, '', 0, false, 'T',);
         }


         $pdf->Output('in_ward.pdf', 'I');
    }

}


class MYPDF extends TCPDF
{
  //Page header
  public function Header()
  {
        $this->SetFont('helvetica', '', 11);
        $this->Ln(10);
        // Title
        $this->Cell(0,8, 'Good Receive Notes', 0, 1, 'L', 0, '', 0, false, 'M', 'M');
        $this->Cell(0, 9, 'Your Mart', 0, 1, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetFont('helvetica', '', 9.5);
        $this->Cell(0, 0, '+92 326 981 0000', 0, 1, 'L', 0, '', 0, false, 'M', 'M');

        $this->Cell(180, 0, '', 'B', 1, 'L', 0, '', 0, false, 'M', 'M');
  }

  // Page footer
  public function Footer()
  {

    $this->Ln(-23);

    $this->SetFont('times', 'U', 9);

    $this->Cell(5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');


    $user_name = auth()->user()->name;
    $date_now = date('d-M-Y h:i A', strtotime(now()));
    $this->SetFont('times', '', 9);
    $this->Cell(5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');

    $this->Ln(7);
    $this->SetFont('times', 'B', 9);
    $this->Cell(180, 0, '*This is an electronically generated report, hence does not require a signature', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
    $this->Cell(180, 0, 'Printed By : ' . $user_name . ' || ' . $date_now, 0, 0, 'C', 0, '', 0, false, 'T', 'M');

  }
}

