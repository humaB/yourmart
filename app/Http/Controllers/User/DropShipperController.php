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
use NumberFormatter;
use TCPDF;

include(public_path() . '/assets/tcpdf/tcpdf.php');

class DropShipperController extends Controller
{
    public function index()
    {
        return view('user.dropshipper');
    }

    public function payOuts()
    {
        return view('user.dropshipper_payouts');
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

    public function pendingPayouts()
    {
        $dropshipper = DropShipper::whereColumn('total_payable', '!=', 'total_paid')->get();

        $totalPayable = DropShipper::sum('total_payable');
        $totalPayablePaid = DropShipper::sum('total_paid');
        $totalRemaining   = DropShipper::sum('remaining_amount');
        $remainingDropshippers = $dropshipper->count();

        $response = [
            'dropshippers' => $dropshipper,
            'total_payable' => $totalPayable,
            'total_paid' => $totalPayablePaid,
            'total_remaining' => $totalRemaining,
            'remaining_dropshippers' => $remainingDropshippers,
        ];

        return (new ResponseCollection($response))
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

    public function update(Request $request)
    {
        // Update Dropshipper Information
        $dropshipper = Dropshipper::findOrFail($request->id);

        if( $dropshipper->email != $request->input('email') ){
            //Check if email is already registered or not for status approved
            $user = User::where('email', $request->input('email'))->first();
            if( $user ){
                return (new ValidationCollection(["This Email already registered with another account"]))
                    ->response()
                    ->setStatusCode(400);
            }
        }

        $dropshipper->update([
            'full_name' => $request->input('full_name'),
            'email' => $request->input('email'),
            'cnic_number' => $request->input('cnic_number'),
            'whatsapp_number' => $request->input('whatsapp_number'),
            'address' => $request->input('address'),
            'account_number' => $request->input('account_number'),
            'account_title' => $request->input('account_title'),
            'account_iban'  => $request->input('account_iban'),
        ]);

        // Loop through the shops and update each record
        foreach ($request->input('shops') as $shopData) {
            // Find the shop by its ID
            $shop = DropShipperShop::findOrFail($shopData['id']);

            // Update the shop details
            $shop->update([
                'store_url' => $shopData['store_url'],
                'social_media_profile_link' => $shopData['social_media_profile_link'],
                'business_description' => $shopData['business_description']
            ]);
        }

        return response()->json(['message' => 'Dropshipper information updated successfully.']);
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
            ->select('account_heads.*', 'account_heads.id as code', 'account_heads.name as label')
            ->get();

        $cash = Cash::join('account_heads', 'cash.account_head_id', 'account_heads.id')
            ->select('account_heads.*', 'account_heads.id as code', 'account_heads.name as label')
            ->get();

        $dropshipper = DropShipper::with('bank')->where('id', $request->id)->first();

        $orders = Order::with('shop')
            ->where('belongs_to', $dropshipper->user_id)
            ->whereIn('status', [8, 9, 10])
            ->whereColumn('total_profit', '!=', 'total_paid_profit')
            ->get();

        $data = [
            'orders'  => $orders,
            'banks'   => $banks,
            'cash'    => $cash,
            'dropshipper' =>  $dropshipper
        ];

        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function shopPayments(Request $request)
    {

        $shop = DropshipperShop::where('id', $request->id)->first();

        $shopPayments = AccountTransaction::where(function ($q) {
            $q->where("type", "BP");
            $q->orWhere("type", "CP");
        })
            ->where("account_head_id", $shop->account_head_id)
            ->orderBy("document_id", 'DESC')
            ->get(["id", "debit", "narration", "created_at"])
            ->map(function ($item) {
                $item->time = date("H:i d-m-Y", strtotime($item->created_at));
                return $item;
            });;


        return response()->json([
            "shopPayments" => $shopPayments,
            "shop" => $shop,
        ]);
    }

    public function paymentHistory(Request $request)
    {

        $dropshipper = DropShipper::where('id', $request->id)->first();

        $ledgers = AccountHead::where('group_id', $dropshipper->group_id)->pluck('id');

        $transactions = AccountTransaction::with('order.shop')->whereIn('account_head_id', $ledgers)
            ->where('type', 'BP')
            ->get();

        return (new ResponseCollection($transactions))
            ->response()
            ->setStatusCode(200);
    }

    public function addPayment(Request $request)
    {
        $request->validate([
            'type'         => ['required'],
            'from_account' => ['required'],
            'amount'       => ['required'],
        ]);

        $dropshipper = DropShipper::where('id', $request->id)->first();

        $orders = Order::with('shop')
            ->where('belongs_to', $dropshipper->user_id)
            ->whereIn('status', [8, 9, 10])
            ->whereColumn('total_profit', '!=', 'total_paid_profit')
            ->get()
            ->sortBy(function ($order) {
                // Calculate the order profit
                $orderProfit = $order->total_profit - $order->total_paid_profit;

                // Prioritize negative profits by returning them as lower values
                return $orderProfit < 0 ? 0 : 1; // Return 0 for negative profits, 1 for positive or zero profits
            })->values(); // Reset the keys to make it a proper collection


        $ledger = new AccountHeadHelper();

        // Initialize remaining amount to the requested amount
        $remainingAmount = $request->amount;
        $document = $ledger->voucherType('bank');

        $attachment = $request->attachment ? $this->attachment($request->attachment) : null;

        foreach ($orders as $order) {
            $shop = $order->shop;

            // Checks account if or not they are open
            // General Ledger
            $group_id = $dropshipper->group_id;
            if (!$dropshipper->group_id) {
                $group = $ledger->accountGroupFourthCreate(
                    $dropshipper->full_name . '-' . $dropshipper->cnic_number,
                    6, // Current asset
                    50, // Account Receivable
                );

                $dropshipper->update([
                    'group_id' => $group->id
                ]);

                $group_id = $group->id;
            }

            $head_id = $shop->account_head_id;
            if (!$shop->account_head_id) {
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

                $head_id = $head->id;
            }

            // Calculate the amount to pay for this order
            $orderProfit = $order->total_profit - $order->total_paid_profit;

            // If the orderProfit is negative, the customer owes money
            if ($orderProfit < 0) {
                // Increase remainingAmount by the amount the customer owes (i.e., the absolute value of the negative profit)
                $remainingAmount += abs($orderProfit);
                // Shop Debit
                $ledger->accountTransaction($head_id, $request->from_account, $orderProfit, 0, $request->narration, $document, $request->type == 'cash' ? 'CP' : 'BP', 'order', $order->id, $approved = 1, $attachment);
                // Optionally update other fields, such as remaining amounts for dropshipper/shop, if required

                //
                $dropshipper->increment('total_paid', $orderProfit);
                $shop->increment('total_paid', $orderProfit);
                $dropshipper->decrement('remaining_amount', $orderProfit);
                $shop->decrement('total_remaining', $orderProfit);
                $order->increment('total_paid_profit', $orderProfit);

                continue; // Skip further processing for this order, as no payment can be made
            }

            if ($remainingAmount <= 0) {
                // If no remaining amount, exit the loop
                break;
            }

            // Determine the amount to pay for this order
            $amountToPay = min($orderProfit, $remainingAmount);

            // Only proceed if there is an amount to pay
            if ($amountToPay > 0) {

                // Shop Debit
                $ledger->accountTransaction($head_id, $request->from_account, $amountToPay, 0, $request->narration, $document, $request->type == 'cash' ? 'CP' : 'BP', 'order', $order->id, $approved = 1, $attachment);

                // Update totals
                $dropshipper->increment('total_paid', $amountToPay);
                $shop->increment('total_paid', $amountToPay);
                $dropshipper->decrement('remaining_amount', $amountToPay);
                $shop->decrement('total_remaining', $amountToPay);
                $order->increment('total_paid_profit', $amountToPay);

                // Reduce the remaining amount
                $remainingAmount -= $amountToPay;
            }
        }

        // Bank Cash Credit
        $ledger->accountTransaction($request->from_account, $head_id, 0, $request->amount, $request->narration, $document, $request->type == 'cash' ? 'CP' : 'BP', 'order', $order->id, $approved = 1, $attachment);

        return response()->json([], 200);
    }

    public function attachment( $image  ){
        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '' ,$filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path            = $image->storeAs('public/uploads/dropshipper/payments/', $nameToStore);
        return $nameToStore;
    }

    public function decision(Request $request)
    {

        $lock = Cache::lock('dropshipper_decision')->block(7, function () use ($request) {

            $dropshipper = DropShipper::with('shop')->where('id', $request->id)->first();
            $shop = DropShipperShop::where('dropshipper_id', $dropshipper->id)->first();
            $group_id = null;
            $head_id = null;

            $checkUser = User::where("email", $dropshipper->email)->first();
            if ($checkUser && $request->action == 'approve') {
                return (new ValidationCollection(["This Email already registered with another account"]))
                    ->response()
                    ->setStatusCode(400);
            }

            if ($request->action == 'deactivate') {
                User::where('id', $dropshipper->user_id)->delete();
                $dropshipper->update([
                    'status'  => '3' // 0 => Pending | 1 => Approved | 2 => Rejected | 3 => Deactivate
                ]);

                return ['message' => 'successfully updated'];
            }

            $leopard = 0;

            if ($request->action != 'reject') {

                $user = User::create([
                    'name'     => $dropshipper->full_name,
                    'email'    => $dropshipper->email,
                    'password' => $dropshipper->password,
                    'role'     => 'dropshipper',
                    'allowed_ip_address' => '*'
                ]);

                $leopardApi = new LeopardApiHelper();
                $leopard  = $leopardApi->createShipperAccount($dropshipper);

                //General Ledger
                $group = $this->accountGroupFourthCreate(
                    $dropshipper->full_name . '-' . $dropshipper->cnic_number,
                    6, // Current asset
                    50, // Account Receivable
                );
                $group_id = $group->id;

                //Shop Ledger
                $head = $this->accountHeadCreate(
                    $shop->store_name . '-' . $shop->dropshipper_id,
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

    function accountGroupFourthCreate($name, $second, $third,)
    {

        $code = AccountGroup::latest('id')->where('parent_id', $third)->limit(1)->value('code') + 1;
        $code = str_pad($code, 3, '0', STR_PAD_LEFT);

        $group = AccountGroup::create([
            'name'       => strtoupper($name),
            'code'       => $code,
            'account_id' =>  $second,
            'parent_id'  => $third,
            'added_by'         => auth()->user()->id
        ]);

        return $group;
    }

    function accountHeadCreate($name, $first, $second, $third, $fourth)
    {

        $code = AccountHead::latest('id')->where('group_id', $fourth)->limit(1)->value('code') + 1;
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

    public function payment_receipt(Request $request)
    {
        $dropshipper = DropShipper::where('id', $request->dropshipper)->first();

        $ledgers = AccountHead::where('group_id', $dropshipper->group_id)->pluck('id');

        $transactions = AccountTransaction::with('order.shop', 'added_by_name')->whereIn('account_head_id', $ledgers)
            ->where('type', 'BP')
            ->where('document_id', $request->document)
            ->get();

        // $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf = new MYPDF2(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('GCH');
        $pdf->SetTitle('Payment Voucher');

        $pdf->project = 'YourMart';
        //GW-JAN-23-CR-1
        $pdf->receipt = 'BP-' . $request->document;
        //$pdf->copy_type = 'Customer Copy';

        // set default header data
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 005', PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $pdf->setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf->SetFont('times', '', 9);

        // add a page
        $pdf->AddPage();
        // set color for background
        $pdf->SetFillColor(255, 255, 127);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setPrintFooter(false);


        // set color for background
        $pdf->SetFillColor(255, 255, 127);
        $pdf->Ln(-20);

        // ---------------------------------------------------------
        $pdf->SetFont('times', 'B', 12);
        $pdf->Cell(160, 0, '', '', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(27, 5, 'Dropshipper Copy', 0, 1, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(15);

        $pdf->SetFont('times', '', 9);

        $Date = date('d-m-Y', strtotime($transactions[0]->created_at));
        $pdf->Cell(8, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(60, 0, '', '', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(10, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(5, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 0, '', '', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(15, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(45, 0, 'Dated : ' . $Date, 'B', 1, 'L', 0, '', 0, false, 'T', 'M');

        $Received_name = strtoupper($dropshipper->full_name);
        $Cnic = $dropshipper->cnic_number;
        $pdf->Ln(3);

        $pdf->Cell(8, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(105, 0, 'Paid To : ' . $Received_name, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(10, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(50, 0, 'CNIC # : ' . $Cnic, 'B', 0, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Ln();

        $Amount = number_format($transactions->sum('debit'));

        $pdf->Ln();
        $pdf->SetFont('times', 'B', 9);

        $pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->Cell(8, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(10, 0, 'Sr No.', 'TL', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(22, 0, 'MOP', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(20, 0, 'Order', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 0, 'Tracking Number', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 0, 'Total Payable', 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(22, 0, 'Cheque #', 'T', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(15, 0, 'Dated ', 'T', 0, 'T', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(25, 0, 'Paid Amount ', 'TR', 1, 'R', 0, '', 0, false, 'T', 'M');

        $pdf->SetFont('times', '', 7.8);
        foreach ($transactions as $i => $posting) {

            $heigth = $pdf->getNumLines($posting->comment, 19);

            $pdf->Cell(8,  $heigth, '', 0, 0, '', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(10,  $heigth, $i + 1, 'L', 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(22,  $heigth, 'Online', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->MultiCell(20, $heigth,  substr($posting->order->shop->store_name, 0, 3) . '-' . $posting->order->order_no, 0, 'L', 0, 0, '', '', true, 0, false, true, false, 'M');
            $pdf->Cell(30,  $heigth, $posting->order->tracking_number, 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(30,  $heigth, number_format($posting->order->total_profit), 0, 0, 'L', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(22,  $heigth, '', 0, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(15,  $heigth, date('d-m-Y', strtotime($posting->created_at)), 0, 0, 'C', 0, '', 0, false, 'T', 'M');
            $pdf->Cell(25,  $heigth, number_format($posting->debit), 'R', 1, 'R', 0, '', 0, false, 'T', 'M');
        }

        $pdf->Cell(8, 8, '', 0, 0, '', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(10, 8, '', 'BL', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(22, 8, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(20, 8, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 8, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(30, 8, '', 'B', 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(22, 8, '', 'B', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(15, 8, '', 'B', 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(25, 8, 'Total Amount : ' . number_format($transactions->sum('debit')), 'BR', 1, 'R', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(3);

        $f = new NumberFormatter("PKR", NumberFormatter::SPELLOUT);

        $pdf->SetFont('times', '', 9);
        $pdf->Cell(8, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(40, 0, 'Amount : ' . number_format($transactions->sum('debit')), 0, 1, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(8, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(180, 0, 'Amount In Words : ' . ucwords($f->format($transactions->sum('debit'))) . " Only", 0, 1, 'L', 0, '', 0, false, 'T', 'M');

        $pdf->Ln();


        $pdf->Ln(2);
        //Close and output PDF document

        $pdf->SetFont('times', 'U', 9);
        $pdf->Cell(15, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(50, 0, $transactions[0]->added_by_name->name, 0, 0, 'C', 0, '', 0, false, 'T', 'M');

        $pdf->Ln();
        //Close and
        $user_name = auth()->user()->name;
        $date_now = date('d-M-Y h:i A', strtotime(now()));
        $pdf->SetFont('times', '', 9);
        $pdf->Cell(15, 0, '', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
        $pdf->Cell(50, 0, 'Prepared By', 0, 0, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(2);
        $pdf->SetFont('times', '', 8);
        $pdf->Cell(180, 0, '* Errors and omissions excepted (E&OE)', 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->Ln(3);
        $pdf->SetFont('times', 'B', 9);
        $pdf->Cell(180, 0, 'Printed By : ' . $user_name . ' || ' . $date_now, 0, 0, 'C', 0, '', 0, false, 'T', 'M');

        $pdf->Output('payment_voucher.pdf', 'I');
    }
}

class MYPDF2 extends TCPDF
{

    public $project;
    public $receipt;
    //Page header
    public function Header()
    {
        $this->Ln(10);

        $image = '';
        $image_path = asset('assets/img/fa-icon.jpg');

        //Logo
        $this->Ln();

        $this->Image($image_path, 20, 10, 15, '', 'JPG',  '', '', true, 150, '', false, false, 0, false, false, false);
        //Project Name
        $this->SetFont('dejavusans', 'B', 12);
        $this->Cell(0, 5, $this->project, 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln();
        $this->SetFont('dejavusans', '', 10);
        $this->Cell(0, 5, 'Receipt # ' . $this->receipt, 0, false, 'C', 0, '', 0, false, 'M', 'M');

        $this->Ln();
    }

    // Page footer
    public function Footer() {}
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
