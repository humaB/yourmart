<?php

namespace App\Http\Controllers\Inventory\Store;

use App\Http\Controllers\Account\Helper\AccountHeadHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Product\ProductQrCode;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use App\Models\Inventory\Store\StoreIssuance;
use App\Models\Inventory\Store\StoreIssuanceDetail;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use TCPDF;

include(public_path() . '/assets/tcpdf/tcpdf.php');
class StoreCheckOutController extends Controller
{
    public function index()
    {
        if (auth()->user()->role != 'admin' && auth()->user()->role != 'order collection manager' && auth()->user()->role != 'supervisor' && auth()->user()->role != 'auditor') {
            abort(401);
        }
        return view('inventory.store.checkout.checkout');
    }

    public function record()
    {
        if (auth()->user()->role != 'admin' && auth()->user()->role != 'order collection manager' && auth()->user()->role != 'inventory manager' && auth()->user()->role != 'supervisor' && auth()->user()->role != 'auditor') {
            abort(401);
        }
        return view('inventory.store.checkout.checkout_record');
    }

    public function fetchRecord()
    {

        $issuance = StoreIssuance::with('order.shop' , 'order.user')
            ->orderBy('id', 'desc')
            ->get();

        return (new ResponseCollection($issuance))
            ->response()
            ->setStatusCode(200);
    }

    public function scannedData(Request $request)
    {

        if( $request->barcode){
            $scanned = ProductQrCode::where('barcode', $request->barcode)->first();

            $product = ProductVariation::with('product.discounts')->where('id', $scanned->product_variation_id)->first();
        }else{
            $product = Product::where('id', $request->id)->first();
            $product = ProductVariation::with('product.discounts')->where('product_id', $product->id)->first();
        }

        return (new ResponseCollection([$product]))
            ->response()
            ->setStatusCode(200);
    }


    public function store(Request $request)
    {
        $lock = Cache::lock('checkout_order')->block(7, function () use ($request) {

            $shop = $request->shop == 0 ? null : $request->shop;
            $dropshipperDetail = DropShipper::where('id', $request->dropshipper)->first();
            $dropshipper = $request->dropshipper == 0 ? 0 : $dropshipperDetail->user_id;
            $city = 0;

            // Create a new order
            $lastShopOrder = Order::where("shop_id", $shop ?? 0)->orderBy('id', 'DESC')->first();
            $lastShopOrder = $lastShopOrder ? $lastShopOrder->order_no + 1 : 1000;
            $order = Order::create([
                'type'      => 'Cash',
                'order_no'  =>   $lastShopOrder,
                'customer_name'  => $request->customer ?? 'Walk in customer',
                'address'        => 'N/A',
                'phone_number'   => $request->phone ?? '',
                'phone_number2'  => '',
                'city_id'        => $city,
                'courier_service_id' => 0,
                'range_id' => 0,
                'courier_service_price' => 0,
                'shop_id'            => $shop ?? 0,
                'instructions'       => '',
                'order_note'       => '',
                'additional_information'  => '',
                'total_bill'         => $request->total, // Save the calculated total bill
                'paid_amount'        => $request->total,
                'remaining_amount'   => 0,
                'discount'           => $request->discount,
                'payment_method'     => ucwords('COD'), // COD || Advance Payment || Partial Payment
                'payment_proof_attachment' =>  $request->file('paymentAttachment') ? $this->attachment($request->file('paymentAttachment')) : null,
                'selling_price'            => 0,
                'packaging_price'          => $request->packing ?? 0,
                'no_of_labels'             => $request->packingQuantity ?? 0,
                'advance_amount'           => $request->total,
                'status'                   => '1',
                'total_weight'             => 0,
                'belongs_to'               => $dropshipper
            ]);

            $subTotal = ( $request->total + (float)$request->discount) - (float)$request->packing ?? 0;

            if($request->products){
                foreach ($request->products as $index => $item) {
                    $quantity = $item['quantity'];
                    $singlePrice =  $item['price'];

                    $totalItemPrice = $singlePrice * $quantity;
                    $discount =  round( ((float)$request->discount /  $subTotal) * (float)$totalItemPrice );
                    $packagingCost =  round( ((float)$request->packing /  $subTotal) * (float)$totalItemPrice );

                    $singlePrice = $singlePrice - ($discount / $quantity);
                    // Create OrderItem
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variation_id' => $item['id'],
                        'price'                => round($singlePrice),
                        'quantity'             => $item['quantity'],
                        'sell_price'           => round( ((float)$item['quantity'] * (float)$singlePrice) + $packagingCost),
                        'packaging_cost'       => $packagingCost,
                        'discount'             => round($discount / $quantity),
                        'belongs_to'           => auth()->user()->id ?? 0
                    ]);

                }
            }
            else{
                OrderItem::create([
                    'order_id'             => $order->id,
                    'product_variation_id' => 0,
                    'price'                => 0,
                    'quantity'             => $request->packingQuantity,
                    'packaging_cost'       => $request->packing,
                    'sell_price'           => $request->packing ?? 0,
                    'belongs_to'           => auth()->user()->id ?? 0
                ]);
            }

        $ledger = new AccountHeadHelper();
        $shop = DropShipperShop::where('id', $shop)->first();

         // General Ledger
         $group_id = $dropshipperDetail->group_id ?? 0;
         if ($dropshipperDetail && !$dropshipperDetail->group_id) {
             $group = $ledger->accountGroupFourthCreate(
                 $dropshipperDetail->full_name . '-' . $dropshipperDetail->cnic_number,
                 6, // Current asset
                 50, // Account Receivable
             );

             $dropshipperDetail->update([
                 'group_id' => $group->id
             ]);

             $group_id = $group->id;
         }

         $head = $shop->account_head_id ?? 74;
         if ($shop && !$shop->account_head_id) {
             // Shop Ledger
             $head = $ledger->accountHeadCreate(
                 $shop->store_name . '-' . $shop->dropshipper_id,
                 1, // Asset
                 6, // Current asset
                 50, // Account Receivable
                 $group_id, // Dropshipper
             );

             $shop->update([
                 'account_head_id' => $head->id
             ]);

             $head = $head->id;
         }

        if((float)$request->cash > 0 ){
            $document = $ledger->voucherType('cash');
            $amount = $request->cash;
            // 74 is sale Ledger
            $ledger->accountTransaction(158, $head, abs($amount), 0, 'Advance Payment received against order', $document, 'CR', 'order', $order->id, $approved = 1);
            //Sale Credit
            $ledger->accountTransaction($head, 158, 0, abs($amount), 'Advance Payment against order', $document, 'CR', 'order', $order->id, $approved = 1);
        }
        if((float)$request->bank > 0 ){
            $document = $ledger->voucherType('bank');
            $ledger->accountTransaction($request->bankAccount, $head, $request->bank, 0, 'Advance Payment received against order', $document, 'BR', 'order', $order->id, $approved = 1);
            //Sale Credit
            $ledger->accountTransaction($head, $request->bankAccount, 0, $request->bank, 'Advance Payment against order', $document, 'BR', 'order', $order->id, $approved = 1);
        }

            return response()->json(['message' => 'Successfully added'], 201);
        });

        return $lock;
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
        $path            = $image->storeAs('public/uploads/dropshipper/payments/', $nameToStore);
        return $nameToStore;
    }

    public function pdf(Request $request)
    {

        $order = Order::where('id', $request->id)
            ->with('items.variation.product')
            ->first();

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        //   $pdf->SetAuthor('Allied Hospital');
        $pdf->SetTitle('YourMart');
        $pdf->SetSubject('Details');
        // remove header and footer default
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // ---------------------------------------------------------

        // set default font subsetting mode
        $pdf->setFontSubsetting(true);

        $pdf->AddPage('P', 'A4');
        $pdf->AddFont('DejaVuSans', '', 'DejaVuSans.php');

        // add content (bill inovice)
        $pdf->SetFont('Helvetica', 'B', '14');
        $pdf->Image('mg/assets/assets/img/logo1.jpg', 10, 5, 35, 18, 'JPG', '', '', false, 300, '', false, false, 0, false, false);
        $pdf->Ln();
        $pdf->Ln(20);
        // Load the necessary font that supports Urdu
        // Replace with your Urdu-supporting font

        $dropshipper = DropShipper::where('user_id', $order->belongs_to ?? 0 )->first();

        $shop = DropShipperShop::with('dropshipper')->where('id', $order->shop_id)->first();
        $order_no = $order->order_no;
        $order_no = $shop ? substr($shop->store_name, 0, 3) . '-' . $order_no : $order_no;

        // Add Urdu text
        $pdf->Cell(125, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('Helvetica', 'B', '11');
        $pdf->Cell(30, 7,  'Order #', 1, 0, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('Helvetica', '', '11');
        $pdf->Cell(40, 7, $order_no, 1, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('DejaVuSans', '', 12);
        $pdf->Cell(120, 0, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        // Header with border
        $pdf->SetFont('Helvetica', 'B', '11');
        if ($order->type == 'Normal') {
            $pdf->Cell(30, 7, 'Tracking #', 1, 0, 'L', 0, '', 0, false, 'T', 'M'); // Increased height
            $pdf->SetFont('Helvetica', '', '11');
            $pdf->Cell(40, 7, $order->tracking_number, 1, 1, 'C', 0, '', 0, false, 'T', 'M'); // Increased height
            $pdf->Cell(25, 7, '', 0, 1, 'C', 0, '', 0, false, 'T', 'M'); // Increased height
        }

        $pdf->Ln();
        $pdf->SetFont('Helvetica', 'B', '14');
        $pdf->Cell(0, 7, 'Bill To', 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('Helvetica', '', '12');
        $pdf->Cell(0, 7, $order->customer_name, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(0, 7, $order->phone_number, 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(2);

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(255, 255, 255); // Background color
        $pdf->SetTextColor(0, 0, 0); // Text color

        // Set column widths
        $widths = [17.5, 70.7, 16.6, 21.2, 21.1, 21.1, 24.8];

        // Move to the first row
        $pdf->Ln(5);

        // Print table header only once on the first page
        if ($pdf->getPage() == 1) {
            $pdf->MultiCell($widths[0], 11, 'Product Image', 1, 'L', true, 0, '', '', true, 0, false, true, 11, 'M');
            $pdf->MultiCell($widths[1], 11, 'Product Title', 1, 'L', true, 0, '', '', true, 0, false, true, 11, 'M');
            $pdf->MultiCell($widths[2], 11, 'Quantity', 1, 'L', true, 0, '', '', true, 0, false, true, 11, 'M');
            $pdf->MultiCell($widths[3], 11, 'Product Cost', 1, 'L', true, 0, '', '', true, 0, false, true, 11, 'M');
            $pdf->MultiCell($widths[4], 11, 'Courier Charges', 1, 'L', true, 0, '', '', true, 0, false, true, 11, 'M');
            $pdf->MultiCell($widths[5], 11, 'Packaging Charges', 1, 'L', true, 0, '', '', true, 0, false, true, 11, 'M');
            $pdf->MultiCell($widths[6], 11, 'Sub Total', 1, 'R', true, 1, '', '', true, 0, false, true, 11, 'M');
        }


        $pdf->SetFont('helvetica', '', 9);
        $html = '
        <table cellpadding="5" style="border-collapse: collapse; width: 100%;">
            <tbody>
        ';

        foreach ($order->items as $item) {
            $title = $item->variation->product->title;
            $quantity = $item->quantity;
            $price = number_format($item->price + $item->discount);
            $courier = number_format($item->courier_cost);
            $packaging = number_format($item->packaging_cost);
            $subTotal = (float)$item->price * (float)$item->quantity;
            $discount = (float)$item->discount * (float)$item->quantity;
            $total_cost = number_format($subTotal + $item->packaging_cost + $item->courier_cost + $discount);
            $image = url('/public/storage/uploads/inventory/products/media/'.$item->variation->product->hero_image);

            $html .= '
                <tr>
                <td width="50" align="center" style="border: 1px solid #888; font-size:8px;"><img src="' . $image . '" width="50px" height="40px"></td>
                <td width="200" align="left" style="border: 1px solid #888; font-size:8px;">' . $title . '</td>
                <td width="47" align="center" style="border: 1px solid #888; font-size:8px;">' . $quantity . '</td>
                <td width="60" align="right" style="border: 1px solid #888; font-size:8px;">' . $price . '</td>
                <td width="60" align="right" style="border: 1px solid #888; font-size:8px;">' . $courier . '</td>
                <td width="60" align="right" style="border: 1px solid #888; font-size:8px;">' . $packaging . '</td>
                <td width="70" align="right" style="border: 1px solid #888; font-size:8px;">' . $total_cost . '</td>
                </tr>
            ';
        }

        $html .= '
            </tbody>
            </table>
            ';

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln();
        $pdf->Ln(10);

        $courierCharges = $order->courier_service_price;
        $packagingCharges = $order->packaging_price;
        $subTotal = $order->total_bill - ( $courierCharges + $packagingCharges ) + $order->discount;

        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(110, 8, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 8, 'Sub Total:', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(40, 8, number_format($subTotal), 0, 1, 'R', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(110, 8, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 8, 'Courier Charges:', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(40, 8, number_format($courierCharges), 0, 1, 'R', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(110, 8, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 8, 'Packaging Charges:', '', 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(40, 8, number_format($packagingCharges), '', 1, 'R', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(110, 8, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 8, 'Discount:', 'B', 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(40, 8, number_format($order->discount), 'B', 1, 'R', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(110, 8, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 8, 'Total :', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(40, 8, number_format($order->total_bill), 0, 1, 'R', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(110, 8, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 8, 'Paid Amount:', 'B', 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(40, 8, number_format($order->paid_amount), 'B', 1, 'R', 0, '', 0, false, 'T', 'M');


        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->Cell(110, 8, '', 0, 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 8, 'Remaining Amount :', 'B', 0, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(40, 8,  number_format($order->remaining_amount), 'B', 1, 'R', 0, '', 0, false, 'T', 'M');

        $pdf->Output('product.pdf', 'I');
    }
}

class MYPDF extends TCPDF
{

    //Page header
    public function Header()
    {
        if ($this->getPage() == 1) {
             // Logo
            $image_path = asset('assets/img/fa-icon.jpg');

            //Logo
            $this->Ln();
            $this->Ln(10);
            $this->Image($image_path, 10, 2, 20, '', 'JPG',  '', '', true, 150, '', false, false, 0, false, false, false);
            $this->Cell(0, 10, 'YourMart', 0, 1, 'C', 0, '', 0, false, 'M', 'M');
            $this->SetFont('helvetica', 'B', 12);
            $this->Cell(0, 10, 'Invoice', 0, 0, 'C', 0, '', 0, false, 'M', 'M');
            $this->SetFont('helvetica', '', 10);
            $this->Cell(0, 10, '+92 326 981 0000', 0, 1, 'R', 0, '', 0, false, 'M', 'M');
            $this->SetFont('helvetica', '', 12);

            $this->Cell(0, 0, "", 'B', 1, 'L', 0, '', 0, false, 'M', 'M');
        }

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
        $this->Cell(0, 0, 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab', 'B', 1, 'C', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 8, 'Developed By - Sar Zone  || Printed By : ' . $user_name . ' || ' . $date_now, 0, 0, 'C', 0, '', 0, false, 'T', 'M');
    }
}
