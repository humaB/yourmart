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
use App\Models\CustomerBank;
use App\Models\Inventory\Order\Order;
use App\Models\User;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperLevel;
use App\Models\User\DropShipperLevelDetail;
use App\Models\User\DropShipperShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

    public function preview()
    {
        return view('user.dropshipper_preview');
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
        $dropshipper = DropShipper::with('general_ledger.dropshipper_shop_ledger.dropshipper_last_paid_voucher')->whereColumn('total_payable', '!=', 'total_paid')->get();

        $totalPayable = DropShipper::sum('total_payable');
        $totalPayablePaid = DropShipper::sum('total_paid');
        $totalRemaining   = DropShipper::sum('remaining_amount');
        $remainingDropshippers = $dropshipper->count();

        $levels = DropShipperLevel::with('dropshipper', 'details')->where('level', '!=', 'New Seller')->get();

        $response = [
            'dropshippers' => $dropshipper,
            'total_payable' => $totalPayable,
            'total_paid' => $totalPayablePaid,
            'total_remaining' => $totalRemaining,
            'remaining_dropshippers' => $remainingDropshippers,
            'levels'   => $levels
        ];

        return (new ResponseCollection($response))
            ->response()
            ->setStatusCode(200);
    }

    public function pendingPayoutRecord(Request $request)
    {
        $dropshippers = Dropshipper::where('total_payable' ,'!=', '0')
            ->orderBy('id', 'desc')
            ->get();

        return (new ResponseCollection($dropshippers))
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
        $level = $request->query('level');
        $incentive = $request->query('incentive');
        $name = $request->query('name');
        $email = $request->query('email');

        $selectDropshippers = [];
        if( $level || $incentive ){
            $selectDropshippers = DropShipperLevel::where('level', $level)->orWhere('is_completed', $incentive)->pluck('dropshipper_id');
        }

        // Apply filters to the query
        $dropshippers = Dropshipper::with([
            'user' => function ($query) {
                $query->withCount(['totalOrders', 'deliveredOrders', 'returnedOrders']);
            },
            'level'
        ])
            ->when($name, function ($query, $name) {
                return $query->where('full_name', 'LIKE', "%$name%");
            })
            ->when($email, function ($query, $email) {
                return $query->where('email', 'LIKE', "%$email%");
            })
            ->when($selectDropshippers, function ($query, $selectDropshippers) {
                return $query->whereIn('id', $selectDropshippers);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($from, function ($query, $from) {
                return $query->whereDate('created_at', '>=', $from);
            })
            ->when($to, function ($query, $to) {
                return $query->whereDate('created_at', '<=', $to);
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        $level = new DropshipperPreviewController();
        $levelTable = new DropShipperLevel();
        $levelDetailTable = new DropShipperLevelDetail();
        foreach ($dropshippers as $dropshipper) {
            $dropshipper = $this->calculateLevel($dropshipper, $level, $levelTable, $levelDetailTable);
        }

        $statuses = DropShipper::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->pluck('count', 'status');

        $statuses = [
            'totalRequests'  => $statuses->sum(),
            'totalPending'   => $statuses->get(0, 0),
            'totalApproved'  => $statuses->get(1, 0),
            'totalRejected'  => $statuses->get(2, 0),
        ];

        $data = [
            'dropshippers'     => $dropshippers,
            'pagination'            => [
                'total'        => $dropshippers->total(),
                'per_page'     => $dropshippers->perPage(),
                'current_page' => $dropshippers->currentPage(),
                'last_page'    => $dropshippers->lastPage(),
                'from'         => $dropshippers->firstItem(),
                'to'           => $dropshippers->lastItem(),
            ],

            'statuses' => $statuses
        ];
        return (new ResponseCollection($data))
            ->response()
            ->setStatusCode(200);
    }

    public function calculateLevel($dropshipper, $level,$levelTable, $levelDetailTable){
        $user = $dropshipper->user;

        $orders = $user->total_orders_count ?? 0;
        $deliveredOrders = $user->delivered_orders_count ?? 0;
        $failedOrders = $user->returned_orders_count ?? 0;

        // Calculate account health
        $accountHealth = $level->accountHealth($deliveredOrders, $failedOrders);

        // You can calculate revenue if available or leave as 0
        $revenue = $dropshipper->total_payable ?? 0;

        // Determine seller level
        $sellerLevel = $level->determineSellerLevel($orders, $accountHealth, $revenue);

        $dropshipper->seller_level = $sellerLevel;

        $check = $levelTable->where('dropshipper_id', $dropshipper->id)->where('level', $sellerLevel)->first();

        if( !$check ){

            $levelTable->where('dropshipper_id', $dropshipper->id)->update([
                'is_active' => '0'
            ]);

            $dropshipperLevel = $levelTable->create([
                'dropshipper_id' => $dropshipper->id,
                'user_id'        => $dropshipper->user_id,
                'level'          => $sellerLevel,// New Seller || Level 01 || Level 02 || Level 03 || Top Rated Seller
                'is_completed'   => $sellerLevel == 'New Seller' ? '1': '0',// 0 => Not Complete || 1 => Completed
            ]);

            $requirementArray = $this->getRequirementArray($sellerLevel);

            $levelDetailTable->create([
                'dropshipper_level_id'   => $dropshipperLevel->id,
                'requirement'            => $requirementArray,
                'is_completed'           => $sellerLevel == 'New Seller' ? '1': '0',// 0 => Not Complete || 1 => Completed
            ]);
        }
    }

    public function getRequirementArray($sellerLevel)
    {
        $allRewards = [
            'Social Media Coverage',
            'Certificate',
            'Gift',
            '1-To-1 Support',
            'Shield of Honor',
            'Membership of Advisory Team',
        ];

        $rewardMap = [
            'New Seller' => [],
            'Level 01' => [
                'Social Media Coverage',
                'Certificate',
            ],
            'Level 02' => [
                'Social Media Coverage',
                'Certificate',
                'Gift',
            ],
            'Level 03' => [
                'Social Media Coverage',
                'Certificate',
                'Gift',
                '1-To-1 Support',
            ],
            'Top Rated Seller' => [
                'Social Media Coverage',
                'Certificate',
                'Gift',
                '1-To-1 Support',
                'Shield of Honor',
                'Membership of Advisory Team',
            ],
        ];

        $available = $rewardMap[$sellerLevel] ?? [];

        return collect($allRewards)
        ->filter(fn($reward) => in_array($reward, $available))
        ->mapWithKeys(fn($reward) => [$reward => ['filled' => false]])
        ->toArray();

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

        $customerBank = CustomerBank::firstOrCreate(
            ['name' => $request->bank['name']], // Conditions to check
        );

        $dropshipper->update([
            'full_name'       => $request->input('full_name'),
            'email'           => $request->input('email'),
            'cnic_number'     => $request->input('cnic_number'),
            'whatsapp_number' => $request->input('whatsapp_number'),
            'address'         => $request->input('address'),
            'bank_id'         => $customerBank->id,
            'account_number'  => $request->input('account_number'),
            'account_title'   => $request->input('account_title'),
            'account_iban'    => $request->input('account_iban'),
            'payment_cycle'   => $request->input('payment_cycle')
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

        // Get the requirement data from the request
        if( $request->level ){
            $requirementData = $request->level['details']['requirement'];

            // Find the DropShipperLevelDetail record
            $detail = DropShipperLevelDetail::where('dropshipper_level_id', $request->level['id'])->first();

            if ($detail) {
                // Prepare updated requirement structure
                $updatedRequirements = [];
                $allFilled = true;

                foreach ($requirementData as $key => $value) {
                    $filled = (bool) ($value['filled'] ?? false);
                    $updatedRequirements[$key] = [
                        'filled' => $filled
                    ];

                    if (!$filled) {
                        $allFilled = false;
                    }
                }

                // Save updated requirement as JSON
                $detail->requirement = $updatedRequirements;
                $detail->is_completed = $allFilled ? 1 : 0;
                $detail->save();

                DropShipperLevel::where('id', $request->level['id'])->update([
                    'is_completed' => $allFilled ? 1 : 0
                ]);
            }
        }

        return response()->json(['message' => 'Dropshipper information updated successfully.']);
    }

    public function dropDown(){

        $dropshippers = DropShipper::where('status', '1')
        ->select('id as code',
                 DB::raw("CONCAT(full_name, ' - ', email) as label"))
        ->get();

        return (new ResponseCollection($dropshippers))
            ->response()
            ->setStatusCode(200);
    }

    public function updateLevels(){
        $dropshippers = DropShipper::with([
            'user' => function ($query) {
                $query->withCount(['totalOrders', 'deliveredOrders', 'returnedOrders']);
            },
        ])->get();

        $level = new DropshipperPreviewController();
        $levelTable = new DropShipperLevel();
        $levelDetailTable = new DropShipperLevelDetail();
        foreach ($dropshippers as $dropshipper) {
            $dropshipper = $this->calculateLevel($dropshipper, $level, $levelTable, $levelDetailTable);
        }

        return response()->json([], 200);
    }

    public function updateLevelRequirements( Request $request ){

            $requirementData = $request->details['requirement'];

            // Find the DropShipperLevelDetail record
            $detail = DropShipperLevelDetail::where('dropshipper_level_id', $request->id)->first();

            if ($detail) {
                // Prepare updated requirement structure
                $updatedRequirements = [];
                $allFilled = true;

                foreach ($requirementData as $key => $value) {
                    $filled = (bool) ($value['filled'] ?? false);
                    $updatedRequirements[$key] = [
                        'filled' => $filled
                    ];

                    if (!$filled) {
                        $allFilled = false;
                    }
                }

                // Save updated requirement as JSON
                $detail->requirement = $updatedRequirements;
                $detail->is_completed = $allFilled ? 1 : 0;
                $detail->save();

                DropShipperLevel::where('id', $request->id)->update([
                    'is_completed' => $allFilled ? 1 : 0
                ]);

        }

        $levels = DropShipperLevel::with('dropshipper', 'details')->where('level', '!=', 'New Seller')->get();
        return (new ResponseCollection($levels))
        ->response()
        ->setStatusCode(200);
    }

    public function filterLevels( Request $request ){
        $levels = DropShipperLevel::with('dropshipper', 'details')->where('level', '!=', 'New Seller')
        ->when($request->level, function($query) use ($request) {
            $query->where('level', $request->level);
        })
        ->when($request->incentive, function($query) use ($request) {
            $query->where('is_completed', $request->incentive);
        })
        ->get();

        return (new ResponseCollection($levels))
        ->response()
        ->setStatusCode(200);
    }

    public function fetchDetails(Request $request)
    {

        $dropshippers = DropShipper::with([
            'user' => function ($query) {
                $query->withCount(['totalOrders', 'deliveredOrders', 'returnedOrders']);
            },
        ])
        ->with('bank', 'city', 'shops', 'level.details')->where('id', $request->id)->get();

        $level = new DropshipperPreviewController();
        $levelTable = new DropShipperLevel();
        $levelDetailTable = new DropShipperLevelDetail();
        foreach ($dropshippers as $dropshipper) {
            $dropshipper = $this->calculateLevel($dropshipper, $level, $levelTable, $levelDetailTable);
        }

        $dropshippers = DropShipper::with('bank', 'city', 'shops', 'level.details')->where('id', $request->id)->get();


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
            ->where(function($query){
                $query->where('type', 'BP')
                ->orWhere('type', 'CP');
            })
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
        $addedAmount = 0;
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
                $addedAmount = abs($orderProfit);
                // Shop Debit
                $ledger->accountTransaction($head_id, $request->from_account, $orderProfit, 0, $request->narration, $document, $request->type == 'cash' ? 'CP' : 'BP', 'order', $order->id, $approved = 1, $attachment);
                // Optionally update other fields, such as remaining amounts for dropshipper/shop, if required

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

                $order->increment('total_paid_profit', $amountToPay);

                // Reduce the remaining amount
                $remainingAmount -= $amountToPay;
            }
        }

        $dropshipper->increment('total_paid', $request->amount);
        $shop->increment('total_paid', $request->amount);
        $dropshipper->decrement('remaining_amount', $request->amount);
        $shop->decrement('total_remaining', $request->amount);

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

        $lock = Cache::lock('dropshipper_decision6')->block(7, function () use ($request) {

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
            }else if($request->action == 'activate'){
                User::where('id', $dropshipper->user_id)->restore();
                $dropshipper->update([
                    'status'  => '1' // 0 => Pending | 1 => Approved | 2 => Rejected | 3 => Deactivate
                ]);
                return ['message' => 'successfully updated'];
            }

            $leopard = 0;

            if ($request->action != 'reject') {

                $leopardApi = new LeopardApiHelper();
                $leopard  = $leopardApi->createShipperAccount($dropshipper);
                if($leopard == 0){
                    return (new ValidationCollection(["Something went wrong please try again"]))
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
                'leopard_id'      => $leopard,
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
            ->where(function($q){
                $q->where('type', 'BP')
                ->orWhere('type', 'CP');
            })
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
        $pdf->receipt = $transactions[0]->type.'-' . $request->document;
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
            $pdf->Cell(22,  $heigth,  $transactions[0]->type == 'BP' ?  'Online' : 'Cash', 0, 0, 'L', 0, '', 0, false, 'T', 'M');
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
        $pdf->Cell(180, 0, 'Printed By : ' . $user_name . ' || ' . $date_now, 0, 1, 'C', 0, '', 0, false, 'T', 'M');
        $pdf->SetFont('times', '', 8);
        $pdf->Cell(0, 0, 'Developed by SAR ZONE', 0, 1, 'C', 0, '', 0, false, 'T', 'M');

        $pdf->Output('payment_voucher.pdf', 'I');
    }

    public function payment_ledger( Request $request ){

         $dropshipper = DropShipper::where('id', $request->dropshipper)->first();

         $orders = Order::with('vouchers', 'shop:id,store_name')->where('belongs_to', $dropshipper->user_id)->whereIn('status', ['8','9','10'])->get();

         $pdf = new MYPDF3(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
         $pdf->SetCreator(PDF_CREATOR);
         $pdf->SetAuthor('');
         $pdf->SetTitle('Payment Ledger');
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

         $pdf->AddPage('L');


         $pdf->SetFont('dejavusans', '', 10, 'C', true);

         $pdf->Ln(5);

         $pdf->SetFont('dejavusans', '', 10, 'C', true);
         $pdf->MultiCell(40, 0, "Printed Date ", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, date('d-M-Y'), 1, 'R', 0, 0);

         $totalPayable = $orders->sum('total_profit');
         $totalPaid= $orders->sum('total_paid_profit');
         $balance= $totalPayable - $totalPaid;


         $pdf->Ln();
         $pdf->MultiCell(40, 0, "Dropshipper", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, $dropshipper->full_name, 1, 'R', 0, 0);
         $pdf->MultiCell(130, 0, "", 0, 'C', 0, 0);
         $pdf->MultiCell(40, 0, "Total Payable", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, $totalPayable, 1, 'R', 0, 0);

         $pdf->Ln();
         $pdf->MultiCell(40, 0, "CNIC", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, $dropshipper->cnic_number, 1, 'R', 0, 0);
         $pdf->MultiCell(130, 0, "", 0, 'C', 0, 0);
         $pdf->MultiCell(40, 0, "Total Paid", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, $totalPaid, 1, 'R', 0, 0);

         $pdf->Ln();
         $pdf->MultiCell(40, 0, "Contact #", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, $dropshipper->whatsapp_number, 1, 'R', 0, 0);
         $pdf->MultiCell(130, 0, "", 0, 'C', 0, 0);
         $pdf->MultiCell(40, 0, "Remaining Balance", 1, 'L', 0, 0);
         $pdf->MultiCell(35, 0, $balance, 1, 'R', 0, 0);

         $pdf->Ln(10);
         $pdf->SetFont('dejavusans', 'B', 8);
         $pdf->Cell(10, 0, "Sr", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(20, 0, "Order #", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(25, 0, "Tracking #", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(22, 0, "Order Date", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(25, 0, "Status", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(35, 0, "Total Payable", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(35, 0, "Paid Date", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(35, 0, "Paid Amount", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(35, 0, "Receipt no", 1, false, 'L', 0, '', 0, false, 'T',);
         $pdf->Cell(35, 0, "Balance", 1, 1, 'L', 0, '', 0, false, 'T',);

         $pdf->SetFont('dejavusans', '', 7.7);
         $total_quantity = 0;
         $orderTbl = '';
       // Iterate over each order
        foreach ($orders as $orderIndex => $order) {
            $vouchers = "";
            $voucherDates = "";
            $voucherAmount = "";

            // Loop through vouchers
            foreach ($order->vouchers as $index => $voucher) {
                $date = date('d-M-Y', strtotime($voucher->created_at));
                $amount = number_format($voucher->debit);
                if( $index == 0){
                    $vouchers .= "<span><u>{$voucher->type} - {$voucher->document_id}</u></span>";
                    $voucherDates .= "<span><u>{$date}</u></span>";
                    $voucherAmount .= "<span><u>{$amount}</u></span>";
                }else{
                    $vouchers .= "<br><span><u>{$voucher->type} - {$voucher->document_id}</u></span>";
                    $voucherDates .= "<br><span><u>{$date}</u></span>";
                    $voucherAmount .= "<br><span><u>{$amount}</u></span>";
                }
            }

            $orderNo = strtoupper(substr($order->shop->store_name, 0, 3)) . '-' . $order->order_no;

            $index = $orderIndex + 1;
            $status = $order->status == '8' ? 'Delivered' : 'Returned';
            $date = date('d-M-Y', strtotime($order->created_at));
            $balances = $order->total_profit - $order->total_paid_profit;
                // Generate the table rows for each order
                $orderTbl .= <<<EOD
                    <table cellspacing="0" cellpadding="4" border="1">
                        <tr>
                            <td style="width:3.6%;">$index</td>
                            <td style="width:7.2%;">  $orderNo</td>
                            <td style="width:9%;">$order->tracking_number</td>
                            <td style="width:8%;">$date</td>
                            <td style="width:9%;">$status</td>
                            <td style="width:12.7%;">$order->total_profit</td>
                            <td style="width:12.6%;">$voucherDates</td>
                            <td style="width:12.6%;">$voucherAmount</td>
                            <td style="width:12.6%;">$vouchers</td>
                            <td style="width:12.7%;">$balances</td>
                        </tr>
                    </table>
                EOD;
        }
        // Write order row to PDF
        $pdf->writeHTML($orderTbl, true, false, false, false, '');

         $pdf->Output('ledger.pdf', 'I');
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


class MYPDF3 extends TCPDF
{

    //Page header
    public function Header()
    {
        $image = '';
        $image_path = asset('assets/img/fa-icon.jpg');

        //Logo
        $this->Ln();

        $this->Image($image_path, 10, 2, 20, '', 'JPG',  '', '', true, 150, '', false, false, 0, false, false, false);
        $this->SetFont('helvetica', 'B', 14);
        $this->Ln(5);
        // Title
        $this->Cell(0, 10, 'YourMart', 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetFont('helvetica', 'B', 12);
        $this->Cell(0, 10, 'Customer Statement', 0, 0, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 10, '+92 326 981 0000', 0, 1, 'R', 0, '', 0, false, 'M', 'M');
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
