<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Resources\ValidationCollection;
use App\Models\City;
use App\Models\Inventory\Courier\CourierDisclaimer;
use App\Models\Inventory\Order\OrderItem;
use App\Models\User\DropShipperShop;
use Illuminate\Support\Facades\Http;

class PostExApiHelper
{
    private $token = 'ZWExMGNhYWFkYjM3NGM3MzhkZWZkN2M0M2M5YjhhZjU6MTY2ZjRiMmQ1YWVmNDkyOTg5OTE5NmUwMTkzNjdiYjg=';
    public function createShipperAccount($dropshipper)
    {
        $storeCode = substr($dropshipper->store_name, 0, 3) .'-'. $dropshipper->id;
        $shipperCode = substr($dropshipper->dropshipper->full_name, 0, 3) . '-' . substr($dropshipper->store_name, 0, 3).'-'.$dropshipper->dropshipper->id;

        $response = Http::withHeaders([
            'token' => $this->token, // Replace with actual token
        ])->post('https://api.postex.pk/services/partnerintegration/api/merchantStore', [
            'merchantStore' => [
                'active'         => true,
                'operationalCity'=> 'Faisalabad',
                'pocContact1'    => $dropshipper->dropshipper->whatsapp_number,
                'pocEmail1'      => $dropshipper->dropshipper->email,
                'pocName'        => $dropshipper->dropshipper->full_name,
                'storeCode'      => $storeCode,
                'storeName'      => $dropshipper->store_name,
            ],
            'merchantStoreAddress' => [
                'address'     => 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab',
                'addressType' => 'Pickup/Return Address',
                'pocContact'  => $dropshipper->dropshipper->whatsapp_number,
                'pocName'     => $dropshipper->dropshipper->full_name,
                'shipperCode' => $shipperCode,
            ],
            'merchantStoreSetting' => [
                'airwayBillPerPage'       => 1,
                'airwayBillSize'          => 2,
                'email1'                  => $dropshipper->dropshipper->email,
                'isStoreShipperAddress'   => true,
                'isStoreShipperContact'   => true,
                'isStoreShipperName'      => true,
                'sendEmail'               => true,
                'sendSms'                 => true,
                'shipperName'             => $dropshipper->dropshipper->full_name,
                'shipperPhone'            => $dropshipper->dropshipper->whatsapp_number,
                'sms1'                    => $dropshipper->dropshipper->whatsapp_number,
            ]
        ]);

        if ( $response->successful() ) {
            return response()->json([
                'error'   => null,
                'success' => 'Dropshipper Created',
                'message' => $storeCode,
            ], 200);
        } else {
            return response()->json([
                'error' => 'Failed to register merchant store',
                'message' => '',
            ], 500);
        }
    }

    public function bookAPacket( $order, $order_no, $shop )
    {

        $city = City::where('id', $order->city_id)->first();
        $orderItems = OrderItem::with('variation.product')->where('order_id', $order->id)->get();

        $description = $orderItems->map(function ($item) {
            $productName = $item->variation->product->title ?? 'Product';
            $sku = $item->variation->sku ?? 'SKU';
            $qty = $item->quantity ?? 1;
            return "{$qty}x {$productName} ({$sku})";
        })->implode(', ');

        $shop = DropShipperShop::with('dropshipper')->where('id', $shop)->first();

        $storeCode = substr($shop->store_name, 0, 3) .'-'. $shop->id;
        $shipperCode = substr($shop->dropshipper->full_name, 0, 3) . '-' . substr($shop->store_name, 0, 3).'-'.$shop->dropshipper->id;

        if( !$shop->postex_store_code ){
           $dropshipper = $this->createShipperAccount($shop);

           $data = $dropshipper->getData(); // returns stdClass
           if ( $data->error  && $data->error  != '') {
                return $data = [
                    'error'  =>  "Something went wrong, please try again",
                ];
            }

            $shop->update([
                'postex_store_code' => $data->message
            ]);
        }
        $order_no = $shop ?  substr($shop->dropshipper->full_name, 0, 3) . '-' . substr($shop->store_name, 0, 3) . '-' . $order_no : $order_no;
        $courierDisclaimer = CourierDisclaimer::where('courier_id',$order->courier_service_id)->first();
        $instruction = $order->instructions ? ($order->instructions. ', Dislaimer : ' . $courierDisclaimer->disclaimer) : ('Dislaimer : ' .$courierDisclaimer->disclaimer ?? "");

        $response = Http::withHeaders([
            'token' => $this->token,
        ])->post('https://api.postex.pk/services/partnerintegration/api/order/create', [
            'customerName'       => $order->customer_name,
            'customerPhone'      => $order->phone_number,
            'deliveryAddress'    => $order->address,
            'invoicePayment'     => $order->selling_price,
            'orderDetail'        => $description,
            'orderRefNumber'     => $order_no,
            'returnAddressCode'  => $shipperCode,
            'cityName'           => 'Lahore',
            'invoiceDivision'    => 1, // Optional
            'items'              => $orderItems->sum('quantity'),
            'orderType'          => 'Normal', // 'Normal', 'Reverse', or 'Overland'
            'remarks'            => $instruction,
            'shipperCode'        => $shipperCode,
            'storeCode'          => $storeCode,
            'transactionNotes'   => $instruction,
        ]);

        // Check response
        if ($response->successful()) {
            $data = $response->json();
            // Process the response data
            $data = [
                'error'        =>  '',
                'track_number' =>  $data['dist']['trackingNumber'],
            ];

            return $data;
        } else {
            return [
                'error' => 'Failed to create PostEx order',
                'details' => $response->body(),
            ];
        }
    }

    public function cancelOrder($trackingNumber){
        $response = Http::withHeaders([
            'token' => $this->token,
        ])->put('https://api.postex.pk/services/partnerintegration/api/order/cancel', [
            'trackingNumbers' => [
                $trackingNumber
            ],
        ]);
    }
}
