<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Mail\DropshipperDecision;
use App\Mail\DropshipperDecisionMail;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Order\Order;
use App\Models\User;
use App\Models\User\DropShipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use TCPDF;

include(public_path() . '/assets/tcpdf/tcpdf.php');

class DropShipperController extends Controller
{
    public function index()
    {
        return view('user.dropshipper');
    }

    public function orderIndex()
    {
        return view('user.dropshipper_order');
    }

    public function orders()
    {
        $orders = Order::with('user')->where('belongs_to', auth()->user()->id)->get();

        return (new ResponseCollection($orders))
            ->response()
            ->setStatusCode(200);
    }

    public function orderDetail(Request $request)
    {
        $orders = Order::with(
            'city',
            'shop',
            'user',
            'items.variation.product',
            'comments.user',
            'items.variation.images.attachment',
            'items.variation.color',
            'items.variation.size'
        )->where('id', $request->id)->get();

        return (new ResponseCollection($orders))
            ->response()
            ->setStatusCode(200);
    }

    public function getRequests()
    {

        $dropshippers = DropShipper::orderBy('id', 'desc')->get();

        return (new ResponseCollection($dropshippers))
            ->response()
            ->setStatusCode(200);
    }

    public function fetchDetails(Request $request)
    {

        $dropshippers = DropShipper::with('bank', 'city', 'shops')->where('id', $request->id)->get();

        return (new ResponseCollection($dropshippers))
            ->response()
            ->setStatusCode(200);
    }

    public function decision(Request $request)
    {

        $lock = Cache::lock('dropshipper_decision')->block(7, function () use ($request) {

            $dropshipper = DropShipper::where('id', $request->id)->first();

            if($request->action == 'deactivate' ){
                User::where('id', $dropshipper->user_id)->delete();
                $dropshipper->update([
                    'status'  => '3' // 0 => Pending | 1 => Approved | 2 => Rejected | 3 => Deactivate
                ]);

                return ['message' => 'successfully updated'];
            }

            if ($request->action == 'approve') {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post('https://merchantapi.leopardscourier.com/api/createShipper/format/json/', [
                    'api_key' => '487F7B22F68312D2C1BBC93B1AEA445B1726751602',
                    'api_password' => 'Allah@001#',
                    'shipment_name'  => $dropshipper->full_name,
                    'shipment_email' => $dropshipper->email, // Optional, can be left empty
                    'shipment_phone' => $dropshipper->whatsapp_number,
                    'shipment_address' => 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab',
                    'city_id' => '322',
                    'cnic' => $dropshipper->cnic, // Optional, can be left empty
                    'return_address' => 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab', // Optional, can be left empty
                ]);
            }

            $leopard = 0;
            // Check if the request was successful
            if ($response->successful()) {
                $data = $response->json();
                $leopard = $data['data']['shipment_id'];
            }

            if ($request->action != 'reject') {
                $checkUser = User::where("email",$dropshipper->email)->first();
                if($checkUser)
                {
                    return (new ValidationCollection(["This Email already registered with another account"]))
                    ->response()
                    ->setStatusCode(400);
                }
                $user = User::create([
                    'name'     => $dropshipper->full_name,
                    'email'    => $dropshipper->email,
                    'password' => $dropshipper->password,
                    'role'     => 'dropshipper',
                    'allowed_ip_address' => '*'
                ]);
            }

            $dropshipper->update([
                'leopard_id' => $leopard,
                'user_id' => $user->id ?? 0,
                'status'  => $request->action == 'reject' ? '2' : '1' // 0 => Pending | 1 => Approved | 2 => Rejected
            ]);

            // Prepare the data
            $mailData = [
                'request'         => $dropshipper->id,
                'full_name'       => $dropshipper->full_name,
                'whatsapp_number' => $dropshipper->whatsapp_number,
                'address'         =>  $dropshipper->address,
                'decision'        => $request->action
            ];

            Mail::to($dropshipper->email)->send(new DropshipperDecisionMail($mailData));


            return ['message' => 'successfully updated'];
        });

        return $lock;
    }

    public function pdf(Request $request)
    {
        // Fetch the DropShipper details with related bank and city data
        $details = DropShipper::with('bank', 'city')->findOrFail($request->id);

        // Create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Company Name');
        $pdf->SetTitle('Dropshipper Form');
        $pdf->SetSubject('Dropshipper Details');

        // Set header and footer data
        $pdf->setFooterData([0, 64, 0], [0, 64, 128]);
        $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins and auto page breaks
        $pdf->SetMargins(10, 40, 10);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Set font for the document
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('dejavusans', '', 10, '', true);

        // Add a page
        $pdf->AddPage();

        // Title and subtitle
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Dropshipper Details', 0, 1, 'C');
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Ln(5);

        // Create HTML content for the details
        $html = '
        <h4>Personal Information</h4>
        <table cellpadding="5" cellspacing="0" border="1">
            <tr>
                <td><strong>Full Name</strong></td>
                <td>' . $details->full_name . '</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>' . $details->email . '</td>
            </tr>
            <tr>
                <td><strong>CNIC Number</strong></td>
                <td>' . $details->cnic_number . '</td>
            </tr>
            <tr>
                <td><strong>WhatsApp Number</strong></td>
                <td>' . $details->whatsapp_number . '</td>
            </tr>
            <tr>
                <td><strong>Address</strong></td>
                <td>' . $details->address . '</td>
            </tr>
            <tr>
                <td><strong>City</strong></td>
                <td>' . ($details->city ? $details->city->name : 'N/A') . '</td>
            </tr>
        </table>

        <br><h4>Bank Information</h4>
        <table cellpadding="5" cellspacing="0" border="1">
            <tr>
                <td><strong>Bank Name</strong></td>
                <td>' . ($details->bank ? $details->bank->name : 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>Account Number</strong></td>
                <td>' . $details->account_number . '</td>
            </tr>
            <tr>
                <td><strong>Account Title</strong></td>
                <td>' . $details->account_title . '</td>
            </tr>
            <tr>
                <td><strong>Account Title</strong></td>
                <td>' . $details->account_iban . '</td>
            </tr>
            <tr>
                <td><strong>Account Title</strong></td>
                <td>' . $details->payment_cycle . '</td>
            </tr>
        </table>
        ';

        // Add shops information in a loop
        if (!empty($details->shops)) {
            foreach ($details->shops as $index => $shop) {
                $html .= '<br><h4>Shop ' . ($index + 1) . ' Details</h4>
        <table cellpadding="5" cellspacing="0" border="1">
            <tr>
                <td><strong>Store Name</strong></td>
                <td>' . ($shop->store_name ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>Store URL</strong></td>
                <td>' . ($shop->store_url ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>Social Media Profile</strong></td>
                <td>' . ($shop->social_media_profile_link ?? 'N/A') . '</td>
            </tr>
            <tr>
                <td><strong>Business Description</strong></td>
                <td>' . ($shop->business_description ?? 'N/A') . '</td>
            </tr>
        </table>';
            }
        }

        // Output the HTML content to the PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Set PDF to display as inline in the browser
        $pdf->Output('dropshipper_form.pdf', 'I');
    }
}

class MYPDF extends TCPDF
{

    //Page header
    public function Header()
    {
        // Logo
        // $image_file = K_PATH_IMAGES . '';
        // $this->Image($image_file, 10, 10, 15, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // // Set font
        $this->SetFont('helvetica', 'B', 14);
        $this->Ln(5);
        // Title
        $this->Cell(0, 15, 'Dropshipper Form', 0, 1, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetFont('helvetica', '', 12);

        $this->Cell(0, 0, "", 'B', 1, 'L', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer()
    {
        $user_name = auth()->user()->name;
        $date_now = date('d-M-Y h:i A', strtotime(now()));
        $this->SetFont('times', '', 9);
        //   Position at 15 mm from bottom
        $this->Ln(-15);
        $this->SetFont('times', '', 8);
        $this->Cell(0, 0, '"Errors and omissions excepted" (E&OE)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetFont('times', 'B', 9);
        $this->Cell(0, 0, 'Printed By : ' . $user_name . ' || ' . $date_now, 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetFont('times', '', 8);
        $this->Cell(0, 0, 'Developed By SAR ZONE', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
    }
}
