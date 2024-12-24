<?php

namespace App\Http\Controllers\Inventory\PurchaseOrder;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Product\Category;
use App\Models\Inventory\Product\Tag;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\PurchaseOrder\PurchaseOrder;
use App\Models\Inventory\PurchaseOrder\PurchaseOrderAttachment;
use App\Models\Inventory\PurchaseOrder\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use TCPDF;
include(public_path().'/assets/tcpdf/tcpdf.php');

class InventoryPurchaseOrderController extends Controller
{
    public function index()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'inventory manager'){
            abort(401);
        }
        return view('inventory.purchase_order.purchase_order');
    }

    public function requests()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'supervisor' ){
             abort(401);
        }

        return view('inventory.purchase_order.purchase_order_requests');
    }

    public function fetchRecord()
    {
        $purchaseOrders = PurchaseOrder::with('supplier:id,full_name')
        ->orderBy('id', 'desc')
        ->get();

        $data = [
            'purchase_orders' => $purchaseOrders,
            'role' => auth()->user()->role
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function fetchAttachment( Request $request )
    {
        $purchaseOrders = PurchaseOrderAttachment::where('po_id', $request->po)->get();

        return (new ResponseCollection($purchaseOrders))
            ->response()
            ->setStatusCode(200);
    }


    public function uploadAttachment( Request $request ){

        $request->validate([
            'po'           => 'required',
            'file'         => 'required',
            'attachment'   => 'required'
        ]);

        PurchaseOrderAttachment::create([
            'po_id'       => $request->po,
            'file'        => $request->file,
            'attachment'  => $request->attachment ? $this->attachment($request->attachment) : '',
        ]);
    }

    public function attachment($image)
    {

        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '', $filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path            = $image->storeAs('public/uploads/purchase_orders/attachments', $nameToStore);
        return $nameToStore;
    }

    public function statusCounts( Request $request )
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

            $numberOfItemsInStock = ProductVariation::sum('stock');
            $stockValue = ProductVariation::all()
            ->sum(function ($variation) {
                return $variation->avg_price * $variation->stock;
            });

            $beingReturnOrders = Order::where('status', '9')
            ->when($request->from, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from);
            })
            ->when($request->to, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to);
            })
            ->pluck('id');

            $totalItemInReturn =  OrderItem::whereIn('order_id', $beingReturnOrders)->sum('quantity');
            $totalReturnValue = OrderItem::with('variation')
                ->whereIn('order_id', $beingReturnOrders)
                ->get()
                ->sum(function ($orderItem) {
                    return $orderItem->quantity * ($orderItem->variation->avg_price ?? 0);
                });

            $grossStockValue =  $stockValue + $totalReturnValue;
            $numberOfProductInStock = ProductVariation::where('stock', '>', 0)->count();
            $outOfStock = ProductVariation::where('stock', '<=', '0')->count();

            $productCounts = Product::selectRaw("
                COUNT(CASE WHEN status = '0' THEN 1 END) as published_products,
                COUNT(CASE WHEN status = '1' THEN 1 END) as draft_products,
                COUNT(CASE WHEN deleted_at IS NOT NULL THEN 1 END) as trashed_products
            ")->first();

            $data = [
                'numberOfItemsInStock'    => $numberOfItemsInStock,
                'stockValue'              => $stockValue,
                'totalItemInReturn'       => $totalItemInReturn,
                'totalReturnValue'        => $totalReturnValue,
                'grossStockValue'         => $grossStockValue,
                'numberOfProductInStock'  => $numberOfProductInStock,
                'outOfStock'              => $outOfStock,
                'publishedProducts'       => $productCounts->published_products,
                'draftProducts'           => $productCounts->draft_products,
                'trashedProducts'         => $productCounts->trashed_products,
                'purchaseOrders'          => $purchaseOrders, // Assuming $purchaseOrders is already defined
            ];

        return (new ResponseCollection([$data]))
            ->response()
            ->setStatusCode(200);
    }

    public function decisions( Request $request ){
        $decision = $request->decision == 'Reject' ? '2' : '1';

        PurchaseOrder::where('id', $request->id)->update([
            'status' => $decision
        ]);

        return ['message' => 'Successfully Updated'];
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validator = \Validator::make($request->all(), [
            'vendor'   => 'required',
        ]);

        $validation = $this->validation($validator);
        if ($validation) {
            return $validation;
        }

        $lock = Cache::lock('create_new_po')->block(7, function () use ($request) {
            // Decode the expenses JSON
            $expenses = json_decode($request->expenses, true);

            // Calculate totals
            $totalAmount = 0;
            foreach ($expenses as $expense) {
                $totalAmount += $expense['rate'] * $expense['qty'];
            }

            // // Create the Purchase Order
            $po = PurchaseOrder::create([
                'supplier_id' => $request->vendor,
                'total_amount' => round( ($totalAmount + $request->tax + $request->deliveryCharges) - $request->discount ),
                'remaining_amount' => round( ($totalAmount + $request->tax + $request->deliveryCharges) - $request->discount ),
                'tax' => round($request->tax),
                'delivery_charges' => round($request->deliveryCharges),
                'discount' => round($request->discount),
                'payment_term_advance' => $request->advance,
                'payment_term_after_delivery' => $request->delivery,
                'status' => 0, // 0 => Pending || 1 => Approved || 2 => Rejected
                'added_by' => auth()->user()->id,
            ]);

            // Loop through each expense to create PurchaseOrderDetail entries
            foreach ($expenses as $expense) {
                $variation = Product::with('variation:id,product_id')->where('id', $expense['product']['code'])->first();

                // Calculate the proportion of this product's total amount to the overall total amount
                $productTotal = $expense['rate'] * $expense['qty'];
                $taxForProduct = ($productTotal / $totalAmount) * $request->tax;
                $discountForProduct = ($productTotal / $totalAmount) * $request->discount;
                $deliveryChargeForProduct = ($productTotal / $totalAmount) * $request->deliveryCharges;
                $total = ($productTotal + $taxForProduct + $deliveryChargeForProduct) - $discountForProduct;
                PurchaseOrderDetail::create([
                    'po_id' => $po->id,
                    'product_id' => $expense['product']['code'],
                    'product_variation_id' => $variation->id,
                    'gate_received_quantity' => 0,
                    'store_received_quantity' => 0,
                    'quantity' => $expense['qty'],
                    'price' => $expense['rate'],
                    'tax' => round($taxForProduct), // Tax distributed based on product proportion
                    'delivery_charges' => round($deliveryChargeForProduct), // Delivery charges distributed based on product proportion
                    'discount' => round($discountForProduct), // Discount distributed based on product proportion
                    'total' => round( $total),
                    'added_by' => auth()->user()->id,
                ]);
            }

            return ['message' => 'Purchase Order Created Successfully'];
        });

        return $lock;
    }

    public function pdf( Request $request ){

            $details = PurchaseOrder::with('approver:id,name','details.product', 'supplier')->where('id', $request->id)->first();

             $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
             $pdf->SetCreator(PDF_CREATOR);
             $pdf->SetAuthor('');
             $pdf->SetTitle('PO Details');
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

             if( $details->status != '1'){
                 $pdf->Ln(10);
                 $pdf->SetFont('dejavusans', '', 14, 'C', true);
                 $pdf->MultiCell(0, 0, 'This purchase order (PO) is not eligible for use.', 0, 'C', 0, 1);
                 $pdf->Ln(10);
             }
             $pdf->Ln(5);

             $pdf->SetFont('dejavusans', '', 10, 'C', true);
             $pdf->MultiCell(40, 0, "PO Date ", 1, 'L', 0, 0);
             $pdf->MultiCell(35, 0, date('d-M-Y', strtotime($details->created_at)), 1, 'R', 0, 0);
             $pdf->MultiCell(15, 0, "", 0, 'C', 0, 0);
             $pdf->MultiCell(37, 0, "", 1, 'L', 0, 0);
             $pdf->MultiCell(30, 0, "", 1, 'R', 0, 1);


             $pdf->MultiCell(40, 0, "PO #", 1, 'L', 0, 0);
             $pdf->MultiCell(35, 0, $details->id, 1, 'R', 0, 0);
             $pdf->MultiCell(15, 0, "", 0, 'C', 0, 0);
             $pdf->MultiCell(37, 0, "Discount", 1, 'L', 0, 0);
             $pdf->MultiCell(30, 0, number_format( $details->discount ), 1, 'R', 0, 1);

             $pdf->MultiCell(40, 0, "", 1, 'L', 0, 0);
             $pdf->MultiCell(35, 0, "", 1, 'R', 0, 0);
             $pdf->MultiCell(15, 0, "", 0, 'C', 0, 0);
             $pdf->MultiCell(37, 0, "Tax", 1, 'L', 0, 0);
             $pdf->MultiCell(30, 0, number_format( $details->tax ), 1, 'R', 0, 1);

             $pdf->MultiCell(40, 0, "", 1, 'L', 0, 0);
             $pdf->MultiCell(35, 0, "", 1, 'R', 0, 0);
             $pdf->MultiCell(15, 0, "", 0, 'C', 0, 0);
             $pdf->MultiCell(37, 0, "Delivery Charges", 1, 'L', 0, 0);
             $pdf->MultiCell(30, 0, number_format( $details->delivery_charges ), 1, 'R', 0, 1);

             $pdf->MultiCell(40, 0, "", 1, 'L', 0, 0);
             $pdf->MultiCell(35, 0, "", 1, 'R', 0, 0);
             $pdf->MultiCell(15, 0, "", 0, 'C', 0, 0);
             $pdf->MultiCell(37, 0, "Total Amount", 1, 'L', 0, 0);
             $pdf->MultiCell(30, 0, number_format( $details->total_amount  ), 1, 'R', 0, 1);


             $pdf->Ln(10);
             $pdf->SetFont('dejavusans', '', 10, 'C', true);
             $pdf->MultiCell(40, 5, "Supplier Detail : ", 0, 'L', 0, 1);
             $pdf->MultiCell(35, 5, 'Company', 1, 'L', 0, 0);
             $pdf->MultiCell(123, 5, $details->supplier->full_name, 1, 'R', 0, 1);

             $pdf->MultiCell(35, 5, 'Name', 1, 'L', 0, 0);
             $pdf->MultiCell(123, 5, $details->supplier->full_name, 1, 'R', 0, 1);

             $pdf->MultiCell(35, 0, "Contact", 1, 'L', 0, 0);
             $pdf->MultiCell(123, 0, $details->supplier->whatsapp_number ? $details->supplier->whatsapp_number : 'N/A', 1, 'R', 0, 1);

             $pdf->MultiCell(35, 0, "Address", 1, 'L', 0, 0);
             $pdf->MultiCell(123, 0, $details->supplier->address ? $details->supplier->address : 'N/A', 1, 'R', 0, 1);


            $pdf->Ln(10);
            $pdf->SetFont('dejavusans', '', 10, 'C', true);
            $pdf->MultiCell(180, 5, "Payment Terms", 0, 'L', 0, 1);
            $pdf->SetFont('dejavusans', '', 9);
            $pdf->Cell(77, 0, "Advance", 1, false, 'C', 0, '', 0, false, 'T',);
            $pdf->Cell(90, 0, $details->payment_term_advance. ' %' , 1, 1, 'C', 0, '', 0, false, 'T',);

            $pdf->Cell(77, 0, "After Delivery", 1, false, 'C', 0, '', 0, false, 'T',);
            $pdf->Cell(90, 0, $details->payment_term_after_delivery. ' %', 1, 1, 'C', 0, '', 0, false, 'T',);

             $pdf->Ln(10);
             $pdf->SetFont('dejavusans', 'B', 8);
             $pdf->Cell(5, 0, "Sr", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(70, 0, "Product Name", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(20, 0, "Price", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(24, 0, "Quantity", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(17, 0, "Tax", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(17, 0, "Delivery", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(18, 0, "Discount", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(30, 0, "Total Amount", 1, 1, 'L', 0, '', 0, false, 'T',);

             $pdf->SetFont('dejavusans', '', 7.7);
             $total_quantity = 0;
             foreach($details->details as $index => $product ){
                 $total_quantity += (float)$product->quantity;
                 $pdf->Cell(5, 9, $index + 1, 1, false, 'L', 0, '', 0, false, 'T',);
                 $pdf->MultiCell(70, 9,$product->product->title ?? '', 1, 'L', 0, 0, '', '', true, 0, false, true, 9, 'M');
                 //$pdf->Cell(70, 9, $product->product->name, 1, false, 'L', 0, '', 0, false, 'T',);
                 $pdf->Cell(20, 9, number_format((float)$product->price, 2), 1, false, 'L', 0, '', 0, false, 'T',);
                 $pdf->Cell(24, 9, $product->quantity, 1, false, 'L', 0, '', 0, false, 'T',);
                 $pdf->Cell(17, 9, number_format($product->tax, 2), 1, false, 'L', 0, '', 0, false, 'T',);
                 $pdf->Cell(17, 9, number_format($product->delivery_charges, 2), 1, false, 'L', 0, '', 0, false, 'T',);
                 $pdf->Cell(18, 9, number_format($product->discount, 2), 1, false, 'L', 0, '', 0, false, 'T',);
                 $pdf->Cell(30, 9, number_format ( ( ( (float)$product->price * (float)$product->quantity ) + (float)$product->tax + (float)$product->delivery_charges) -  (float)$product->discount, 2), 1, 1, 'R', 0, '', 0, false, 'T',);
             }

             $pdf->Cell(5, 5, "", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(70, 5, "", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(20, 5, "Total QTY", 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(24, 5, $total_quantity, 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(17, 5, '', 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(17, 5, '', 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(18, 5, '', 1, false, 'L', 0, '', 0, false, 'T',);
             $pdf->Cell(30, 5,'Total : '. number_format ( $details->total_amount  ), 1, 1, 'R', 0, '', 0, false, 'T',);


             $pdf->Output('purchase_order.pdf', 'I');
    }


    private function validation($validator)
    {

        if ($validator->fails()) {

            $validationErrors = [];
            $errors = $validator->errors()->all();

            foreach ($errors as $error) {
                array_push($validationErrors, $error);
            }

            return (new ValidationCollection($validationErrors))
                ->response()
                ->setStatusCode(400);
        }
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
        $this->Cell(0,8, 'Purchase Order', 0, 1, 'L', 0, '', 0, false, 'M', 'M');
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
