<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Account\Helper\AccountHeadHelper;
use App\Models\Account\AccountTransaction;
use App\Models\Inventory\Courier\CourierCategory;
use App\Models\Inventory\Courier\CourierDisclaimer;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Order\OrderLeopardStatus;
use App\Models\Inventory\Product\Setting\OtherCharge;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeopardApiHelper
{

    /*
    *   FORMAT
        {
        "data": [
            {
            "cn_number": "FS0875827294",
            "status": "PN1",
            "receiver_name": null,
            "reason": "NEED BLOCK/SECTOR/PHASE #",
            "activity_date": "2025-07-12 17:16:55",
            "booked_packet_order_id": "Muh-Dis-1000"
            }
        ]
    }
    */
    private $apiKey = '487F7B22F68312D2C1BBC93B1AEA445B1726751602';
    private $apiPassword = 'Allah@001#';
    public $shipmentStatuses = [
        'RC' => [
            'leopard_id' => 'Consignment Booked (Shipment Scanned In LCS Facility)',
            'label' => 'In Process'
        ],
        'PN1' => [
            'leopard_id' => 'First Attempt In Forward Leg',
            'label' => 'In Process'
        ],
        'PN2' => [
            'leopard_id' => 'Second Attempt In Forward Leg',
            'label' => 'In Process'
        ],
        'RN1' => [
            'leopard_id' => 'First Attempt In Reverse Leg',
            'label' => 'In Process'
        ],
        'RN2' => [
            'leopard_id' => 'Second Attempt In Reverse Leg',
            'label' => 'In Process'
        ],
        'RW' => [
            'leopard_id' => 'Returned to Warehouse (Terminal Status)',
            'label' => 'In Process'
        ],
        'DW' => [
            'leopard_id' => 'Delivered to Warehouse (Terminal Status)',
            'label' => 'In Process'
        ],
        'DR' => [
            'leopard_id' => 'Delivered to Vendor (Terminal Status)',
            'label' => 'In Process'
        ],
        'AR' => [
            'leopard_id' => 'Arrived At Station',
            'label' => 'In Process'
        ],
        'DP' => [
            'leopard_id' => 'Dispatched',
            'label' => 'In Process'
        ],
        'SP' => [
            'leopard_id' => 'Shipment Picked',
            'label' => 'In Process'
        ],
        'DV' => [
            'leopard_id' => 'Delivered (Terminal Status)',
            'label' => 'Delivered'
        ],
        'RO' => [
            'leopard_id' => 'Being Return',
            'label' => 'Return'
        ],
        'RS' => [
            'leopard_id' => 'Returned to Shipper (Terminal Status)',
            'label' => 'Return'
        ],
        'NR' => [
            'leopard_id' => 'Ready for Return',
            'label' => 'Ready for Return'
        ],
        'AC' => [
            'leopard_id' => 'Assigned To Courier (Out For Delivery)',
            'label' => 'Out for Delivery'
        ]
    ];


    public function createShipperAccount($dropshipper)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://merchantapi.leopardscourier.com/api/createShipper/format/json/', [
            'api_key' => $this->apiKey,
            'api_password' => $this->apiPassword,
            'shipment_name'  => $dropshipper->shop->store_name,
            'shipment_email' => $dropshipper->email, // Optional, can be left empty
            'shipment_phone' => $dropshipper->whatsapp_number,
            'shipment_address' => 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab',
            'city_id' => '322',
            'cnic' => $dropshipper->cnic, // Optional, can be left empty
            'return_address' => 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab', // Optional, can be left empty
        ]);

        $leopard = 0;
        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();
            $leopard = $data['data']['shipment_id'];
        }

        return $leopard;
    }

    public function bookAPacket($weight, $request, $order_no, $shop, $city, $package)
    {
        $package = CourierCategory::where('id', $package)->first();
        $courierDisclaimer = CourierDisclaimer::where('courier_id', $request->courier_service_id)->first();
        $instruction = $request->instructions ? ($request->instructions . ', Dislaimer : ' . $courierDisclaimer->disclaimer) : ('Dislaimer : ' . $courierDisclaimer->disclaimer ?? "");


        $shop = DropShipperShop::with('dropshipper')->where('id', $shop)->first();
        $order_no = $shop ?  substr($shop->dropshipper->full_name, 0, 3) . '-' . substr($shop->store_name, 0, 3) . '-' . $order_no : $order_no;

        $weight_in_kg = $weight; // Assuming $weight is in kilograms
        $weight_in_grams = $weight_in_kg * 1000;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://merchantapi.leopardscourier.com/api/bookPacket/format/json/', [
            'api_key' => $this->apiKey,
            'api_password' => $this->apiPassword,
            'booked_packet_weight' => $weight_in_grams, // Replace with actual weight in grams

            'booked_packet_no_piece' => 1, // Replace with actual number of pieces
            'booked_packet_collect_amount' => $request->selling_price, // Replace with actual collection amount
            'booked_packet_order_id' => $order_no, // Optional, replace if needed
            'origin_city' => 'self', // Replace with 'self' or integer value for city
            'destination_city' => $city->courier_city_id, // Replace with 'self' or integer value for city

            'shipment_id' => $shop->leopard_id == 0 ? $shop->dropshipper->leopard_id : $shop->leopard_id, // Replace with actual shipment ID
            'shipment_name_eng' => $shop->store_name, // Replace with 'self' or custom name
            'shipment_email' => $shop->dropshipper->email, // Replace with 'self' or custom email
            'shipment_phone' => $shop->dropshipper->whatsapp_number, // Replace with 'self' or custom phone number
            'shipment_address' => 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab', // Replace with 'self' or custom address

            'consignment_name_eng' => $request->customer_name, // Replace with consignee name
            'consignment_phone' => $request->phone_number, // Replace with consignee phone number
            'consignment_address' => $request->address,  // Replace with consignee address
            'special_instructions' => $instruction, // Replace with actual instructions

            'shipment_type' => strtolower($package->name), // Optional Field (You can keep it empty so It will pick default value i.e. "overnight"), Type Shipment type name here

            'return_address' => 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab', // Optional, can be empty
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();
            // Process the response data
            $data = [
                'error'        =>  $data['error'],
                'track_number' =>  $data['track_number'],
                'slip_link'    =>  $data['slip_link'],
            ];

            return $data;
        }
    }

    public function webHook($request)
    {
        $data = $request['data'];


        // Sort the data array by 'activity_date' in ascending order
        usort($data, function ($a, $b) {
            return strtotime($a['activity_date']) <=> strtotime($b['activity_date']);
        });

        Log::info($request);
        foreach ($data as $order) {

            $detail = Order::with('range')->where('tracking_number', $order['cn_number'])->first();
            if (isset($this->shipmentStatuses[$order['status']]) && $detail && $detail->status != 8 && $detail->status != 9) {
                $status = $this->shipmentStatuses[$order['status']];

                OrderLeopardStatus::updateOrCreate(
                    [
                        'order_id'   => $detail->id,       // Condition 1: order_id must match
                        'short_code' => $order['status'],  // Condition 2: short_code must match
                    ],
                    [
                        'leopard_label'  => $status['leopard_id'],
                        'internal_label' => $status['label'],
                        'receiver_name'  => $order['receiver_name'],
                        'reason'         => $order['reason'],
                        'time'           => $order['activity_date'],
                    ]
                );

                $link = env('MIX_WEB_URL') . 'dropshipper/orders';

                $order_no = substr($detail->shop->store_name, 0, 3) . '-' . $detail->order_no;

                //If product is delivered
                if ($status['label'] == 'Delivered' && $detail->status != '8') {
                    $response = Http::get('https://merchantapi.leopardscourier.com/api/getShippingCharges/format/json/', [
                        'api_key'      => $this->apiKey,
                        'api_password' => $this->apiPassword,
                        'cn_numbers'   => $order['cn_number'], // or 'XXYYYYYYYY,XXYYYYYYYY,XXYYYYYY'
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();

                        $courierCharges = ceil($data['data'][0]['net_charges'] * 1.16 ?? 0);

                        if ($courierCharges > 0) {
                            $totalBill = $detail->product_cost + $courierCharges + $detail->courier_service_internal_price + $detail->packaging_price + $detail->subtotal_tax;
                            $totalRemaining = $totalBill - $detail->paid_amount;
                            $shippingTax = ($courierCharges + $detail->courier_service_internal_price + $detail->packaging_price) * 0.02;
                            $taxOnProfit = (($detail->selling_price + $detail->advance_amount) - ($totalBill - $detail->subtotal_tax)) * 0.02;

                            $detail->update([
                                'total_bill'            => ceil($totalBill + $shippingTax),
                                'remaining_amount'      => ceil($totalRemaining + $shippingTax),
                                'courier_service_price' => ceil($courierCharges + $detail->courier_service_internal_price),
                                'shipping_tax'          => ceil($shippingTax),
                                'profit_tax'            => ceil($taxOnProfit)
                            ]);

                            $items = OrderItem::where('order_id', $detail->id)->get();
                            $totalCourierAmount = $detail->courier_service_price;
                            $subTotal = $detail->product_cost;

                            foreach ($items as $item) {
                                $totalItemPrice = $item->price * $item->quantity;
                                // 181  / 2963 * 1950 =
                                $extraCourierCharges = ((float)$totalCourierAmount / (float)$subTotal) * (float)$totalItemPrice;

                                $taxOnProfit   = ((float)$detail->profit_tax / (float)$subTotal) * (float)$totalItemPrice;
                                $taxOnShipping = ((float)$detail->shipping_tax / (float)$subTotal) * (float)$totalItemPrice;

                                $item->update([
                                    'courier_cost'   => round($extraCourierCharges),
                                    'shipping_tax'   => round($taxOnShipping),
                                    'tax_on_profit'  => round($taxOnProfit)
                                ]);
                            }
                        }
                    }
                    $this->parcelDelivered($detail);
                    $detail->update([
                        'status' => '8'
                    ]);

                    NotificationHelper::addNotification(
                        $title = 'Order Delivered',
                        $messge = "Order $order_no has been delivered successfully.",
                        $link = $link,
                        $image = null,
                        $directImage = null,
                        $color    = 'green',
                        $isPublic = 1,
                        $user = $detail->belongs_to
                    );
                }
                //If product is not delivered and returned
                if ($status['leopard_id'] == 'Being Return' && $detail->status != '9') {

                    $dropshipper = DropShipper::where('user_id', $detail->belongs_to)->first();
                    $shop = DropShipperShop::where('id', $detail->shop_id)->first();

                    $response = Http::get('https://merchantapi.leopardscourier.com/api/getShippingCharges/format/json/', [
                        'api_key'      => $this->apiKey,
                        'api_password' => $this->apiPassword,
                        'cn_numbers'   => $order['cn_number'], // or 'XXYYYYYYYY,XXYYYYYYYY,XXYYYYYY'
                    ]);

                    if ($response->successful()) {
                        $data = $response->json();

                        $courierCharges = ceil($data['data'][0]['net_charges'] * 1.16 ?? 0);

                        if ($courierCharges > 0) {
                            $totalBill = $detail->product_cost + $courierCharges + $detail->courier_service_internal_price + $detail->packaging_price + $detail->subtotal_tax;
                            $totalRemaining = $totalBill - $detail->paid_amount;
                            $shippingTax = ($courierCharges + $detail->courier_service_internal_price + $detail->packaging_price) * 0.02;
                            $taxOnProfit = (($detail->selling_price + $detail->advance_amount) - ($totalBill - $detail->subtotal_tax)) * 0.02;

                            $detail->update([
                                'total_bill'            => ceil($totalBill + $shippingTax),
                                'remaining_amount'      => ceil($totalRemaining + $shippingTax),
                                'courier_service_price' => ceil($courierCharges + $detail->courier_service_internal_price),
                                'shipping_tax'          => ceil($shippingTax),
                                'profit_tax'            => ceil($taxOnProfit)
                            ]);

                            $items = OrderItem::where('order_id', $detail->id)->get();
                            $totalCourierAmount = $detail->courier_service_price;
                            $subTotal = $detail->product_cost;

                            foreach ($items as $item) {
                                $totalItemPrice = $item->price * $item->quantity;
                                // 181  / 2963 * 1950 =
                                $extraCourierCharges = ((float)$totalCourierAmount / (float)$subTotal) * (float)$totalItemPrice;

                                $taxOnProfit   = ((float)$detail->profit_tax / (float)$subTotal) * (float)$totalItemPrice;
                                $taxOnShipping = ((float)$detail->shipping_tax / (float)$subTotal) * (float)$totalItemPrice;

                                $item->update([
                                    'courier_cost'   => round($extraCourierCharges),
                                    'shipping_tax'   => round($taxOnShipping),
                                    'tax_on_profit'  => round($taxOnProfit)
                                ]);
                            }
                        }
                    }
                    $this->parcelCancel($dropshipper, $shop, $detail);

                    $detail->update([
                        'status' => '9'
                    ]);

                    NotificationHelper::addNotification(
                        $title = 'Order Returned',
                        $messge = "Order $order_no is marked as returned by the courier.",
                        $link = $link,
                        $image = null,
                        $directImage = null,
                        $color    = 'red',
                        $isPublic = 1,
                        $user = $detail->belongs_to
                    );
                }

                if ($order['status'] == 'AC') {
                    $detail->update([
                        'status' => '11'
                    ]);
                }

                if ($order['status'] == 'NR') {
                    //Ready to return
                    $detail->update([
                        'status' => '12'
                    ]);

                    NotificationHelper::addNotification(
                        $title = 'Re-Attempt Request Active',
                        $messge = "Order $order_no marked for re-attempt. Call customer and apply it.",
                        $link = $link,
                        $image = null,
                        $directImage = null,
                        $color    = 'orange',
                        $isPublic = 1,
                        $user = $detail->belongs_to
                    );
                }
            }
        }
    }

    public function parcelDelivered($order)
    {

        //Customer Selling Price - ( (Product Price + courier + packaging) - Advance )
        //3500 - ( ( 1000 + 200 + 40 ) - 500)
        $courierCharges = $order->courier_service_price;
        $packingCharges = $order->packaging_price;
        $advance         = $order->paid_amount;
        $productPrice    = (float)$order->product_cost;

        $courierExtraCharges = $order->range->our_charges;
        $payableAmount = (float)$order->total_bill - $advance; //Amount Yourmart must receive
        $profit      = (((float)$order->selling_price + $advance) - (float)$order->total_bill) - $order->profit_tax;


        $dropshipper = DropShipper::where('user_id', $order->belongs_to)->first();
        $shop = DropShipperShop::where('id', $order->shop_id)->first();
        $this->accountOnDelivered($dropshipper, $shop, $order, $productPrice, $packingCharges, $courierCharges, $courierExtraCharges);

        //Check if order is Replacement or normal
        if ($order->is_replacement == '0') {
            $order->decrement('remaining_amount', $payableAmount);
            $order->increment('paid_amount', $payableAmount);
            $order->update([
                'total_profit'  => $profit
            ]);

            $dropshipper->increment('total_payable', $profit);
            $dropshipper->increment('remaining_amount', $profit);

            $shop->increment('total_payable', $profit);
            $shop->increment('total_remaining', $profit);
        }
    }

    public function parcelCancel($dropshipper, $shop, $order)
    {

        $ledger = new AccountHeadHelper();
        $document = $ledger->voucherType('JV');

        //(courier + packaging) + 60 charges
        $courierCharges = $order->courier_service_price;
        $packingCharges = $order->packaging_price;
        $advance         = $order->paid_amount;
        $courierExtraCharges = $order->range->our_charges;
        $otherCharges = OtherCharge::where('type', 'Return')->where('status', '0')->first();
        $otherCharges = (float)$otherCharges->amount;

        //60 are extra charges which will in future be set by admin
        $dropshipper->decrement('total_payable', $courierCharges + $packingCharges +  $otherCharges);
        $dropshipper->decrement('remaining_amount', $courierCharges + $packingCharges +  $otherCharges);

        $shop->decrement('total_payable', $courierCharges + $packingCharges +  $otherCharges);
        $shop->decrement('total_remaining', $courierCharges + $packingCharges +  $otherCharges);

        $remainingPayable = $advance - ($courierCharges + $packingCharges + $otherCharges);
        $order->update([
            'total_profit'  => $remainingPayable
        ]);

        //Checks account if or not they are open
        //General Ledger
        $group_id = $dropshipper->group_id;
        if (!$dropshipper->group_id) {
            $group_id = $this->openDropShipperLedger($ledger, $dropshipper);
        }

        $head_id = $shop->account_head_id;
        if (!$shop->account_head_id) {
            $head_id = $this->openShopLedger($shop, $ledger, $group_id);
        }

        //Book Total Packaging and Courier + 60 RS as charge
        /*
        *   Total Packaging and Courier + 60 Debit to Dropshipper
        *   Total Packaging and Courier + 60 Credit in leopard and sales account
        */
        $document = $ledger->voucherType('JV');
        $ledger->accountTransaction($head_id, 74, $courierCharges + $packingCharges + $otherCharges, 0, 'Total Receivable Amount', $document, 'JV', 'order', $order->id, $approved = 1);
        //Leopard Credit
        $ledger->accountTransaction(73, $head_id, 0, $courierCharges -  $courierExtraCharges, 'Courier Charges', $document, 'JV', 'order', $order->id, $approved = 1);
        //Sale Credit
        $ledger->accountTransaction(74, $head_id, 0, $packingCharges + $otherCharges + $courierExtraCharges, 'Packaging Charges', $document, 'JV', 'order', $order->id, $approved = 1);

        //Advance payment Entry if
        /*
        *   Advance Amount Debit to Bank
        *   Advance Amount Credit to Dropshipper
        */
        //Meezan Bank Debit
        if ($order->paid_amount > 0) {
            $document = $ledger->voucherType('bank');
            //Bank Cash Debit
            $ledger->accountTransaction(75, $head_id, $order->paid_amount, 0, 'Advance Payment received against order', $document, 'BR', 'order', $order->id, $approved = 1);
            //Dropshipper Credit
            $ledger->accountTransaction($head_id, 75, 0, $order->paid_amount, 'Advance Payment against order', $document, 'BR', 'order', $order->id, $approved = 1);

            $dropshipper->increment('total_payable', $order->paid_amount);
            $dropshipper->increment('remaining_amount', $order->paid_amount);

            $shop->increment('total_payable', $order->paid_amount);
            $shop->increment('total_remaining', $order->paid_amount);
        }
    }

    private function accountOnDelivered($dropshipper, $shop, $order, $productPrice, $packingCharges, $courierCharges, $courierExtraCharges)
    {

        $ledger = new AccountHeadHelper();
        $document = $ledger->voucherType('JV');

        //Checks account if or not they are open
        //General Ledger
        $group_id = $dropshipper->group_id;
        if (!$dropshipper->group_id) {
            $group_id = $this->openDropShipperLedger($ledger, $dropshipper);
        }

        $head_id = $shop->account_head_id;
        if (!$shop->account_head_id) {
            $head_id = $this->openShopLedger($shop, $ledger, $group_id);
        }

        //Book Total Payable
        /*
        *   If order is replacement 'Total Receivable Amount' will Debit in product replacement expense account_head = 164
        *   Else Total Amount Debit to Dropshipper
        *   Total Amount Credit in Sales
        *
        */
        $document = $ledger->voucherType('JV');
        $mainAccount = $order->is_replacement == '1' ? 164 : $head_id;
        $ledger->accountTransaction($mainAccount, 74, $order->total_bill, 0, $order->is_replacement == '1' ? 'Expense amount on company for product replacement' : 'Total Receivable Amount for order #' . $order->order_no, $document, 'JV', 'order', $order->id, $approved = 1);
        //Sale Credit
        $ledger->accountTransaction(74, $mainAccount, 0, $productPrice + $packingCharges + $courierExtraCharges, 'Product + Packaging Cost', $document, 'JV', 'order', $order->id, $approved = 1);
        //leopard Credit
        $ledger->accountTransaction(73, $mainAccount, 0, ($courierCharges - $courierExtraCharges) + ($order->shipping_tax + $order->subtotal_tax), 'Courier Cost & Tax', $document, 'JV', 'order', $order->id, $approved = 1);

        //Advance payment Entry if
        /*
        *   Advance Amount Debit to Bank
        *   Advance Amount Credit to Dropshipper
        */
        //Meezan Bank Debit
        if ($order->paid_amount > 0) {
            $document = $ledger->voucherType('bank');
            $ledger->accountTransaction(75, $head_id, $order->paid_amount, 0, 'Advance Payment received against order # ' . $order->order_no, $document, 'BR', 'order', $order->id, $approved = 1);
            //Sale Credit
            $ledger->accountTransaction($head_id, 75, 0, $order->paid_amount, 'Advance Payment against order # ' . $order->order_no, $document, 'BR', 'order', $order->id, $approved = 1);
        }

        if ($order->is_replacement == '1' && $order->paid_amount > 0) {
            //Adjust advance payment with expense
            /*
            *   Advance Amount Debit to Dropshipper as this is expense on his/her end
            *   Expense Account 164 debit to decrease expense
            */
            $document = $ledger->voucherType('JV');
            $ledger->accountTransaction($head_id, 164, $order->paid_amount, 0, 'Advance Payment adjusted against courier and packaging expense', $document, 'JV', 'order', $order->id, $approved = 1);
            //Sale Credit
            $ledger->accountTransaction(164, $head_id, 0, $order->paid_amount, 'Advance Payment adjusted against courier and packaging expense', $document, 'JV', 'order', $order->id, $approved = 1);
        }

        //When Leopard Received Payment
        /*
        *   Leopard Debit with Selling Price
        *   Dropshipper Credit with selling price
        */
        $document = $ledger->voucherType('JV');
        if ($order->selling_price != 0) {
            $ledger->accountTransaction(73, $head_id, $order->selling_price, 0, 'COD amount received from customer, order # ' . $order->order_no, $document, 'JV', 'order', $order->id, $approved = 1);
            //Sale Credit
            $ledger->accountTransaction($head_id, 73, 0, $order->selling_price, 'COD amount received from customer, order # ' . $order->order_no, $document, 'JV', 'order', $order->id, $approved = 1);
        }
    }

    private function openShopLedger($shop, $ledger, $group)
    {
        //Shop Ledger
        $head = $ledger->accountHeadCreate(
            $shop->store_name . '-' . $shop->dropshipper_id,
            1, // Asset
            6, // Current asset
            50, // Account Receivable
            $group, // Dropshipper
        );

        $shop->update([
            'account_head_id' => $head->id
        ]);

        return $head_id = $head->id;
    }

    private function openDropShipperLedger($ledger, $dropshipper)
    {

        $group = $ledger->accountGroupFourthCreate(
            $dropshipper->full_name . '-' . $dropshipper->cnic_number,
            6, // Current asset
            50, // Account Receivable
        );

        $dropshipper->update([
            'group_id' => $group->id
        ]);

        return $group_id = $group->id;
    }

    public function reverseAccountOnDelivered($order)
    {

        $transactions = AccountTransaction::where('posting_type', 'order')
            ->where('posting_id', $order->id)
            ->where(function ($q) {
                $q->where('type', 'JV')->orWhere('type', 'BR');
            })
            ->delete();

        $payableAmount = (float)$order->total_bill - ($advance->advance_amount ?? 0);
        $profit      = $order->total_profit;

        $order->increment('remaining_amount', $payableAmount);
        $order->decrement('paid_amount', $payableAmount);
        $order->update([
            'total_profit'  => 0,
        ]);

        $dropshipper = DropShipper::where('user_id', $order->belongs_to)->first();
        $shop = DropShipperShop::where('id', $order->shop_id)->first();

        $dropshipper->decrement('total_payable', $profit);
        $dropshipper->decrement('remaining_amount', $profit);

        $shop->decrement('total_payable', $profit);
        $shop->decrement('total_remaining', $profit);
    }
}
