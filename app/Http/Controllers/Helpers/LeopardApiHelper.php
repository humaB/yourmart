<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Account\Helper\AccountHeadHelper;
use App\Models\Inventory\Courier\CourierCategory;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderLeopardStatus;
use App\Models\Inventory\Product\Setting\OtherCharge;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperShop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LeopardApiHelper
{
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
            'label' => 'Return'
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
            'special_instructions' => $request->instructions ?? '', // Replace with actual instructions

            'shipment_type' => strtolower($package->name), // Optional Field (You can keep it empty so It will pick default value i.e. "overnight"), Type Shipment type name here

            'return_address' => 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab', // Optional, can be empty
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            $data = $response->json();
            // Process the response data
            $data = [
                'track_number' =>  $data['track_number'],
                'slip_link'    =>  $data['slip_link'],
            ];

            return $data;
        }
    }

    public function webHook($request){
        $data = $request['data'];

        // Sort the data array by 'activity_date' in ascending order
        usort($data, function ($a, $b) {
            return strtotime($a['activity_date']) <=> strtotime($b['activity_date']);
        });

        foreach( $data as $order ){
            $detail = Order::with('range')->where('tracking_number', $order['cn_number'])->first();
            if (isset($this->shipmentStatuses[$order['status']]) && $detail ) {
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

                //If product is delivered
                if( $status['label'] == 'Delivered' && $detail->status != '8'){
                    $this->parcelDelivered($detail);
                    $detail->update([
                        'status' => '8'
                    ]);
                }
                  //If product is delivered
                if( $status['leopard_id'] == 'Being Return' && $detail->status != '9'){
                    $dropshipper = DropShipper::where('user_id', $detail->belongs_to)->first();
                    $shop = DropShipperShop::where('id', $detail->shop_id)->first();
                    $this->parcelCancel($dropshipper, $shop, $detail);

                    $detail->update([
                        'status' => '9'
                    ]);
                }

                if( $order['status'] == 'AC'){
                    $detail->update([
                        'status' => '11'
                    ]);
                }
            }
        }
    }

    private function parcelDelivered( $order ){

        //Customer Selling Price - ( (Product Price + courier + packaging) - Advance )
        //3500 - ( ( 1000 + 200 + 40 ) - 500)
        $courierCharges = $order->courier_service_price;
        $packingCharges = $order->packaging_price;
        $advance         = $order->paid_amount;
        $productPrice    = (float)$order->total_bill - ( (float)$courierCharges + (float)$packingCharges );
        $sellingPrice    = $order->selling_price - (float)$order->total_bill + $advance;
        $courierExtraCharges = $order->range->our_charges;
        $payableAmount = (float)$order->total_bill - $advance;
        $profit      = ((float)$order->selling_price + $advance) - (float)$order->total_bill;

        $dropshipper = DropShipper::where('user_id', $order->belongs_to)->first();
        $shop = DropShipperShop::where('id', $order->shop_id)->first();
        $this->accountOnDelivered( $dropshipper, $shop, $order, $productPrice, $packingCharges, $courierCharges, $courierExtraCharges );

        $order->decrement('remaining_amount' , $payableAmount);
        $order->increment('paid_amount' , $payableAmount);
        $order->update([
            'total_profit'  => $profit
        ]);

        $dropshipper->increment('total_payable' , $sellingPrice);
        $dropshipper->increment('remaining_amount' , $sellingPrice);

        $shop->increment('total_payable' , $sellingPrice);
        $shop->increment('total_remaining' , $sellingPrice);


    }

    private function parcelCancel( $dropshipper, $shop , $order ){

        $ledger = new AccountHeadHelper();
        $document = $ledger->voucherType('JV');

        //(courier + packaging) + 60 charges
        $courierCharges = $order->courier_service_price;
        $packingCharges = $order->packaging_price;
        $advance         = $order->paid_amount;
        $courierExtraCharges = $order->range->our_charges;

        //60 are extra charges which will in future be set by admin
        $dropshipper->decrement('total_payable' , $courierCharges + $packingCharges + 60);
        $dropshipper->decrement('remaining_amount' , $courierCharges + $packingCharges + 60);

        $shop->decrement('total_payable' , $courierCharges + $packingCharges + 60);
        $shop->decrement('total_remaining' , $courierCharges + $packingCharges + 60);

        $remainingPayable = $advance - ( $courierCharges + $packingCharges + 60 );
        $order->update([
            'total_profit'  => $remainingPayable
        ]);

        //Checks account if or not they are open
        //General Ledger
        $group_id = $dropshipper->group_id;
        if( !$dropshipper->group_id ){
            $group_id = $this->openDropShipperLedger( $ledger, $dropshipper);
        }

        $head_id = $shop->account_head_id;
        if( !$shop->account_head_id ){
            $head_id = $this->openShopLedger( $shop , $ledger, $group_id);
        }

        $otherCharges = OtherCharge::where('type', 'Return')->where('status', '0')->first();

        //Book Total Packaging and Courier + 60 RS as charge
        /*
        *   Total Packaging and Courier + 60 Debit to Dropshipper
        *   Total Packaging and Courier + 60 Credit in leopard and sales account
        */
        $document = $ledger->voucherType('JV');
        $ledger->accountTransaction($head_id, 74, $courierCharges + $packingCharges + $otherCharges , 0, 'Total Receivable Amount', $document, 'JV', 'ORDER', $order->id, $approved = 1);
        //Leopard Credit
        $ledger->accountTransaction(73, $head_id, 0, $courierCharges -  $courierExtraCharges, 'Courier Charges', $document, 'JV', 'ORDER', $order->id, $approved = 1);
        //Sale Credit
        $ledger->accountTransaction(74, $head_id, 0, $packingCharges + $otherCharges + $courierExtraCharges, 'Packaging Charges', $document, 'JV', 'ORDER', $order->id, $approved = 1);

           //Advance payment Entry if
        /*
        *   Advance Amount Debit to Bank
        *   Advance Amount Credit to Dropshipper
        */
        //Meezan Bank Debit
        if($order->paid_amount > 0 ){
            $document = $ledger->voucherType('bank');
            //Bank Cash Debit
            $ledger->accountTransaction(75, $head_id, $order->paid_amount, 0, 'Advance Payment received against order', $document, 'BR', 'ORDER', $order->id, $approved = 1);
            //Dropshipper Credit
            $ledger->accountTransaction($head_id, 75, 0, $order->paid_amount, 'Advance Payment against order', $document, 'BR', 'ORDER', $order->id, $approved = 1);

            $dropshipper->increment('total_payable' , $order->paid_amount);
            $dropshipper->increment('remaining_amount' , $order->paid_amount);

            $shop->increment('total_payable' , $order->paid_amount);
            $shop->increment('total_remaining' , $order->paid_amount);
        }

    }

    private function accountOnDelivered( $dropshipper, $shop , $order, $productPrice , $packingCharges, $courierCharges, $courierExtraCharges){

        $ledger = new AccountHeadHelper();
        $document = $ledger->voucherType('JV');

        //Checks account if or not they are open
        //General Ledger
        $group_id = $dropshipper->group_id;
        if( !$dropshipper->group_id ){
            $group_id = $this->openDropShipperLedger( $ledger, $dropshipper);
        }

        $head_id = $shop->account_head_id;
        if( !$shop->account_head_id ){
            $head_id = $this->openShopLedger( $shop , $ledger, $group_id);
        }

        //Book Total Payable
        /*
        *   Total Amount Debit to Dropshipper
        *   Total Amount Credit in Sales
        */
        $document = $ledger->voucherType('JV');
        $ledger->accountTransaction($head_id, 74, $order->total_bill, 0, 'Total Receivable Amount', $document, 'JV', 'ORDER', $order->id, $approved = 1);
        //Sale Credit
        $ledger->accountTransaction(74, $head_id, 0, $productPrice + $packingCharges + $courierExtraCharges, 'Product + Packaging Cost', $document, 'JV', 'ORDER', $order->id, $approved = 1);
        //leopard Credit
        $ledger->accountTransaction(73, $head_id, 0, $courierCharges - $courierExtraCharges, 'Courier Cost', $document, 'JV', 'ORDER', $order->id, $approved = 1);

        //Advance payment Entry if
        /*
        *   Advance Amount Debit to Bank
        *   Advance Amount Credit to Dropshipper
        */
        //Meezan Bank Debit
        if($order->paid_amount > 0 ){
            $document = $ledger->voucherType('bank');
            $ledger->accountTransaction(75, $head_id, $order->paid_amount, 0, 'Advance Payment received against order', $document, 'BR', 'ORDER', $order->id, $approved = 1);
            //Sale Credit
            $ledger->accountTransaction($head_id, 75, 0, $order->paid_amount, 'Advance Payment against order', $document, 'BR', 'ORDER', $order->id, $approved = 1);
        }

        //When Leopard Received Payment
        /*
        *   Leopard Debit with Selling Price
        *   Dropshipper Credit with selling price
        */
        $document = $ledger->voucherType('JV');
        $ledger->accountTransaction(73, $head_id, $order->selling_price, 0, 'COD amount received from customer', $document, 'JV', 'ORDER', $order->id, $approved = 1);
        //Sale Credit
        $ledger->accountTransaction($head_id, 73, 0, $order->selling_price, 'COD amount received from customer', $document, 'JV', 'ORDER', $order->id, $approved = 1);
    }

    private function openShopLedger( $shop , $ledger, $group){
            //Shop Ledger
            $head = $ledger->accountHeadCreate(
                $shop->store_name.'-'.$shop->dropshipper_id,
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

    private function openDropShipperLedger( $ledger, $dropshipper){

        $group = $ledger->accountGroupFourthCreate(
            $dropshipper->full_name.'-'.$dropshipper->cnic_number,
            6, // Current asset
            50, // Account Receivable
        );

        $dropshipper->update([
            'group_id' => $group->id
        ]);

        return $group_id = $group->id;
    }

}
