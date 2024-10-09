<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Account\Helper\AccountHeadHelper;
use App\Http\Controllers\Helpers\LeopardApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Mail\DropshipperDecision;
use App\Models\Account\Bank;
use App\Mail\DropshipperDecisionMail;
use App\Models\Account\AccountGroup;
use App\Models\Account\Cash;
use App\Models\Account\AccountHead;
use App\Http\Resources\ValidationCollection;
use App\Models\Account\AccountTransaction;
use App\Models\Inventory\Order\Order;
use App\Models\User;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperShop;
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

    public function getRequests(Request $request)
    {

        $status = $request->query('status');
        $from = $request->query('from');
        $to = $request->query('to');

        // Apply filters to the query
        $dropshippers = Dropshipper::when($status, function ($query, $status) {
            return $query->where('status', $status);
        })
        ->when($from, function ($query, $from) {
            return $query->whereDate('created_at', '>=', $from);
        })
        ->when($to, function ($query, $to) {
            return $query->whereDate('created_at', '<=', $to);
        })
        ->orderBy('id', 'desc')
        ->get();

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

    public function paymentData(Request $request)
    {
        $banks = Bank::join('account_heads', 'banks.account_head_id', 'account_heads.id')
        ->select('account_heads.*','account_heads.id as code', 'account_heads.name as label')
        ->get();
        $cash = Cash::join('account_heads', 'cash.account_head_id', 'account_heads.id')
            ->select('account_heads.*','account_heads.id as code', 'account_heads.name as label')
            ->get();

        $shops = DropshipperShop::where('dropshipper_id',$request->id)->get(["id as code","store_name as label"]);

        return response()->json([
            "shops" => $shops,
            "banks" => $banks,
            "cash" => $cash,
        ]);
    }

    public function shopPayments(Request $request)
    {
        $shop = DropshipperShop::where('id',$request->id)->first();

        $shopPayments = AccountTransaction::
        where(function($q){
            $q->where("type","BP");
            $q->orWhere("type","CP");
        })
        ->where("account_head_id",$shop->account_head_id)
        ->orderBy("document_id",'DESC')
        ->get(["id","debit","narration","created_at"])
        ->map(function ($item) {
            $item->time = date("H:i d-m-Y",strtotime($item->created_at));
            return $item;
        });;


        return response()->json([
            "shopPayments" => $shopPayments,
            "shop" => $shop,
        ]);
    }

    public function addPayment(Request $request)
    {
        $request->validate([
            'type' => ['required'],
            'shop_id' => ['required'],
            'from_account' => ['required'],
            'amount' => ['required'],
        ]);

        $shop = DropshipperShop::where('id',$request->shop_id)->first();
        $dropshipper = Dropshipper::where('id', $shop->dropshipper_id)->first();

        $ledger = new AccountHeadHelper();

        //Checks account if or not they are open
        //General Ledger
        $group_id = $dropshipper->group_id;
        if( !$dropshipper->group_id ){
            $group = $ledger->accountGroupFourthCreate(
                $dropshipper->full_name.'-'.$dropshipper->cnic_number,
                6, // Current asset
                50, // Account Receivable
            );

            $dropshipper->update([
                'group_id' => $group->id
            ]);

            $group_id = $group->id;
        }


        $head_id = $shop->account_head_id;
        if( !$shop->account_head_id ){
            //Shop Ledger
            $head = $ledger->accountHeadCreate(
                $shop->store_name.'-'.$shop->dropshipper_id,
                1, // Asset
                6, // Current asset
                50, // Account Receivable
                $group_id, // Dropshipper
            );

            $shop->update([
                'account_head_id' => $head->id
            ]);

            $head_id = $head->id;
        }

        $document = $ledger->voucherType('bank');
        //Shop Debit
        $ledger->accountTransaction($head_id,$request->from_account, $request->amount, 0, $request->narration, $document, $request->type == 'cash' ? 'CP' : 'BP', 'shop', $shop->id, $approved = 1);
        //Bank Cash Credit
        $ledger->accountTransaction($request->from_account, $head_id, 0, $request->amount, $request->narration, $document, $request->type == 'cash' ? 'CP' : 'BP', 'shop', $shop->id, $approved = 1);


        $dropshipper->increment('total_paid', $request->amount);
        $shop->increment('total_paid', $request->amount);

        $dropshipper->decrement('remaining_amount', $request->amount);
        $shop->decrement('total_remaining', $request->amount);

        return response()->json([],200);
    }

    public function decision(Request $request)
    {

        $lock = Cache::lock('dropshipper_decision')->block(7, function () use ($request) {

            $dropshipper = DropShipper::with('shop')->where('id', $request->id)->first();
            $shop = DropShipperShop::where('dropshipper_id', $dropshipper->id)->first();
            $group_id = null;
            $head_id = null;

            if( $request->action == 'deactivate' ){
                User::where('id', $dropshipper->user_id)->delete();
                $dropshipper->update([
                    'status'  => '3' // 0 => Pending | 1 => Approved | 2 => Rejected | 3 => Deactivate
                ]);

                return ['message' => 'successfully updated'];
            }

            $leopard = 0;
            if ($request->action == 'approve') {
                $leopardApi = new LeopardApiHelper();
                $leopard  = $leopardApi->createShipperAccount( $dropshipper );
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

                //General Ledger
                $group = $this->accountGroupFourthCreate(
                    $dropshipper->full_name.'-'.$dropshipper->cnic_number,
                    6, // Current asset
                    50, // Account Receivable
                );
                $group_id = $group->id;

                //Shop Ledger
                $head = $this->accountHeadCreate(
                    $shop->store_name.'-'.$shop->dropshipper_id,
                    1, // Asset
                    6, // Current asset
                    50, // Account Receivable
                    $group->id, // Dropshipper
                );
                $head_id = $head->id;
            }

            $dropshipper->update([
                'user_id' => $user->id ?? 0,
                'group_id' => $group_id,
                'status'  => $request->action == 'reject' ? '2' : '1' // 0 => Pending | 1 => Approved | 2 => Rejected
            ]);

            DropShipperShop::where('dropshipper_id', $dropshipper->id)->update([
                'leopard_id' => $leopard,
                'account_head_id' => $head_id,
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

    function accountGroupFourthCreate($name, $second, $third,) {

        $code = AccountGroup::latest('id')->where('parent_id', $third )->limit(1)->value('code') + 1;
        $code = str_pad($code, 3, '0', STR_PAD_LEFT);

        $group = AccountGroup::create([
            'name'       => strtoupper($name),
            'code'       => $code,
            'account_id' =>  $second,
            'parent_id'  => $third,
            'company_id'  => 0,
            'added_by'         => auth()->user()->id
        ]);

        return $group;
    }

    function accountHeadCreate($name, $first, $second, $third, $fourth) {

        $code = AccountHead::latest('id')->where('group_id', $fourth )->limit(1)->value('code') + 1;
        $code = str_pad($code, 4, '0', STR_PAD_LEFT);

        $head = AccountHead::create([
            'name' => strtoupper($name),
            'code' => $code,
            'parent_account_id' => $first,
            'account_id' => $second,
            'parent_group_id' => $third,
            'group_id' => $fourth,
            'added_by' => auth()->user()->id
        ]);

        return $head;
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
