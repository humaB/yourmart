<?php

namespace App\Http\Controllers\Inventory\Gate;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Gate\GateInward;
use App\Models\Inventory\Gate\GateInwardDetail;
use App\Models\Inventory\PurchaseOrder\PurchaseOrder;
use App\Models\Inventory\PurchaseOrder\PurchaseOrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use TCPDF;
include(public_path().'/assets/tcpdf/tcpdf.php');

class GateInWardController extends Controller
{
    public function index()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'gate incharge'){
            abort(401);
        }
        return view('inventory.gate.gate_pending_purchase_order');
    }

    public function record()
    {
        if( auth()->user()->role != 'admin' && auth()->user()->role != 'gate incharge'){
            abort(401);
        }
        return view('inventory.gate.gate_inward_record');
    }

    public function pendingPO(){

        $data = PurchaseOrderDetail::whereColumn('quantity', '!=', 'gate_received_quantity')
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

        $data = GateInwardDetail::with('product', 'igp')
        ->orderBy('id','desc')
        ->get();

        return (new ResponseCollection($data))
        ->response()
        ->setStatusCode(200);
    }

    public function inWard( Request $request ){
        $lock = Cache::lock('gate_inward')->block(7, function () use ($request) {
            $products = json_decode( $request['details']);

            $igp = GateInward::create([
                'po_id'     => $request['po'],
                'added_by'  => auth()->user()->id,
            ]);


            foreach( $products as $product ){
                if( $product ){
                    $data = PurchaseOrderDetail::where('id', $product->id )->first();
                    if( $product->qty == 0 ){
                        continue;
                    }
                    if( $data->quantity >=  $data->gate_received_quantity +  $product->qty){
                        PurchaseOrderDetail::where('id', $product->id)->increment('gate_received_quantity' , $product->qty);

                        GateInwardDetail::create([
                            'igp_id'     => $igp->id,
                            'product_id' => $data->product_id,
                            'quantity'   => $product->qty,
                            'added_by'   => auth()->user()->id,
                        ]);
                    }else{
                        return ( new ValidationCollection ( ['Quantity added should be less than Receiveable'] ) )
                        ->response()
                        ->setStatusCode( 409 );
                    }
                }
            }

        });

        return $lock;
    }

    public function pdf( Request $request ){

        $details = GateInward::with('details.product')
        ->where('id', $request->id)
        ->first();

         $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
         $pdf->SetCreator(PDF_CREATOR);
         $pdf->SetAuthor('');
         $pdf->SetTitle('IGP Details');
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
         $pdf->MultiCell(40, 0, "IGP Date ", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, date('d-M-Y', strtotime($details->created_at)), 1, 'R', 0, 0);



        $pdf->Ln();
         $pdf->MultiCell(40, 0, "IGP #", 1, 'L', 0, 0);
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
        $this->Cell(0,8, 'Gate In ward', 0, 1, 'L', 0, '', 0, false, 'M', 'M');
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

