<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Resources\ValidationCollection;
use App\Models\City;
use App\Models\Inventory\Courier\CourierDisclaimer;
use App\Models\Inventory\Order\Order;
use App\Models\Inventory\Order\OrderItem;
use App\Models\Inventory\Order\OrderLeopardStatus;
use App\Models\User\DropShipper;
use App\Models\User\DropShipperShop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostExApiHelper
{
    /*
    *   FORMAT
        {
            'trackingNumber' => '21142430000415',
            'orderReferenceNumber' => 'Asm-UpT-1012',
            'statusUpdateDatetime' => '2025-05-09 16:50:16.0',
            'orderStatus' => 'Unbooked',
            'returnRequested' => false,
            'lastAttemptReason' => NULL,
        }
    */
    private $token = 'ZWExMGNhYWFkYjM3NGM3MzhkZWZkN2M0M2M5YjhhZjU6MTY2ZjRiMmQ1YWVmNDkyOTg5OTE5NmUwMTkzNjdiYjg=';
    private $url = 'https://api.postex.pk/services/partnerintegration/api';

    public $shipmentStatuses = [
        'Unbooked' => [
            'postex' => 'Unbooked',
            'label' => 'In Process'
        ],
        'Booked' => [
            'postex' => 'Booked',
            'label' => 'In Process'
        ],
        'PostEx WareHouse' => [
            'postex' => 'PostEx WareHouse',
            'label' => 'In Process'
        ],
        'Out For Delivery' => [
            'postex' => 'Out For Delivery',
            'label' => 'Out For Delivery'
        ],
        'Delivered' => [
            'postex' => 'Delivered',
            'label' => 'Delivered'
        ],
        'Returned' => [
            'postex' => 'Returned',
            'label' => 'Returned'
        ],
        'Un-Assigned By Me' => [
            'postex' => 'Un-Assigned By Me',
            'label' => 'Cancelled'
        ],
        'Expired' => [
            'postex' => 'Expired',
            'label' => 'In Process'
        ],
        'Delivery Under Review' => [
            'postex' => 'Delivery Under Review',
            'label' => 'Re-Attempt - Active'
        ],
        'Picked By PostEx' => [
            'postex' => 'Picked By PostEx',
            'label' => 'In Process'
        ],
        'Out For Return' => [
            'postex' => 'Out For Return',
            'label' => 'Returned'
        ],
        'Attempted' => [
            'postex' => 'Attempted',
            'label' => 'In Process'
        ],
        'En-Route to {city} warehouse' => [
            'postex' => 'En-Route to {city} warehouse',
            'label' => 'In Process'
        ]
    ];

    public function createShipperAccount($dropshipper)
    {
        $storeCode = substr($dropshipper->store_name, 0, 3) . '-' . $dropshipper->id;
        $shipperCode = substr($dropshipper->dropshipper->full_name, 0, 3) . '-' . substr($dropshipper->store_name, 0, 3) . '-' . $dropshipper->id;

        $response = Http::withHeaders([
            'token' => $this->token,
        ])->post($this->url . '/merchantStore', [
            'merchantStore' => [
                'active'         => true,
                'operationalCity' => 'Faisalabad',
                'pocContact1'    => $dropshipper->dropshipper->whatsapp_number,
                'pocEmail1'      => $dropshipper->dropshipper->email,
                'pocName'        => $dropshipper->store_name,
                'storeCode'      => $storeCode,
                'storeName'      => $dropshipper->store_name,
            ],
            'merchantStoreAddress' => [
                'address'     => 'P-22, College Road, Near Hockey Stadium, Kohinoor Town, Faisalabad, Punjab',
                'addressType' => 'Pickup/Return Address',
                'pocContact'  => $dropshipper->dropshipper->whatsapp_number,
                'pocName'     => $dropshipper->store_name,
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
                'shipperName'             => $dropshipper->store_name,
                'shipperPhone'            => $dropshipper->dropshipper->whatsapp_number,
                'sms1'                    => $dropshipper->dropshipper->whatsapp_number,
            ]
        ]);

        if ($response->successful()) {
            return response()->json([
                'error'   => null,
                'success' => 'Dropshipper Created',
                'message' => $storeCode,
            ], 200);
        } else {
            return response()->json([
                'error' => 'Failed to register merchant store',
                'message' => $response['statusMessage'],
            ], 500);
        }
    }

    public function bookAPacket($order, $order_no, $shop)
    {

        $city = City::where('id', $order->city_id)->first();
        $orderItems = OrderItem::with('variation.product')->where('order_id', $order->id)->get();

        $description = $orderItems->map(function ($item) {
            $productName = $item->variation->product->title ?? 'Product';
            $sku = $item->variation->sku ?? 'SKU';
            $qty = $item->quantity ?? 1;
            return "{$qty}x {$productName} ({$sku})";
        })->implode(', ');

        if (strlen($description) >= 500) {
            $description = $orderItems->map(function ($item) {
                $sku = $item->variation->sku ?? 'SKU';
                $qty = $item->quantity ?? 1;
                return "{$qty}x ({$sku})";
            })->implode(', ');
        }

        $shop = DropShipperShop::with('dropshipper')->where('id', $shop)->first();

        $storeCode = substr($shop->store_name, 0, 3) . '-' . $shop->id;
        $shipperCode = substr($shop->dropshipper->full_name, 0, 3) . '-' . substr($shop->store_name, 0, 3) . '-' . $shop->id;

        if (!$shop->postex_store_code) {
            $dropshipper = $this->createShipperAccount($shop);

            $data = $dropshipper->getData(); // returns stdClass
            if ($data->error  && $data->error  != '') {
                return $data = [
                    'error'  =>  $data->message,
                ];
            }

            $shop->update([
                'postex_store_code' => $data->message
            ]);
        }
        $order_no = $shop ?  substr($shop->dropshipper->full_name, 0, 3) . '-' . substr($shop->store_name, 0, 3) . '-' . $order_no : $order_no;
        $courierDisclaimer = CourierDisclaimer::where('courier_id', $order->courier_service_id)->first();
        $instruction = $order->instructions ? ($order->instructions . ', Dislaimer : ' . $courierDisclaimer->disclaimer) : ('Dislaimer : ' . $courierDisclaimer->disclaimer ?? "");

       $response = Http::withHeaders([
            'token' => $this->token,
        ])->post($this->url . '/order/create', [
            'customerName'       => $order->customer_name,
            'customerPhone'      => $order->phone_number,
            'deliveryAddress'    => $order->address,
            'invoicePayment'     => $order->selling_price,
            'orderDetail'        => $description,
            'orderRefNumber'     => $order_no,
            'returnAddressCode'  => $shipperCode,
            'cityName'           => $city->name,
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

    public function reAttempt($trackingNumber)
    {
        return $response = Http::withHeaders([
            'token' => $this->token,
        ])
        ->patch($this->url ."/order/retry-attempt", [
                'trackingNumber' => $trackingNumber
        ]);
    }

    public function cancelOrder($trackingNumber)
    {
        return $response = Http::withHeaders([
            'token' => $this->token,
        ])->patch($this->url . '/order/cancel', [
            'trackingNumbers' => [
                $trackingNumber
            ],
        ]);
    }

    public function tracking($trackingNumber)
    {
        $response = Http::withHeaders([
            'token' => $this->token,
        ])->get($this->url . '/order/track/' . $trackingNumber);

        $buffer = $response->json();

        $tracking = collect($buffer['dist']['transactionStatusHistory'])->map(function ($label) {
            return [
                'id'    => 0,
                'leopard_label' => $label->transactionStatusMessage,
                'reason'        => "",
                'receiver_name' => ""
            ];
        })->toArray();

        return $tracking;
    }

    public function webHook($request)
    {
        $order = $request;

        return $detail = Order::with('range')->where('tracking_number', trim($order['trackingNumber']))->first();

        if( $detail && $detail->status == 8 ){
            $lastUpdatedStatus = OrderLeopardStatus::where('order_id', $detail->id)->orderBy('id', 'desc')->first();
            if($lastUpdatedStatus && $lastUpdatedStatus->created_at >= now()->subHours(24)){
                // Code to run if last updated status is within the last 24 hours
                $helper = new LeopardApiHelper();
                $helper->reverseAccountOnDelivered($detail);
                $detail->update([
                    'status' => '11'
                ]);
            }
        }
        if (isset($this->shipmentStatuses[$order['orderStatus']]) && $detail && $detail->status != 8 && $detail->status != 9) {

            $status = $this->shipmentStatuses[$order['orderStatus']];
            $link = env('MIX_WEB_URL').'dropshipper/orders';

            $order_no = substr($detail->shop->store_name, 0, 3) . '-' . $detail->order_no;
            $trackingNumber = $order['trackingNumber'];

            //If product is delivered
            if ($status['label'] == 'Delivered' && $detail->status != '8') {

                $response = Http::withHeaders([
                    'token' => $this->token,
                ])->get($this->url . "/payment/cpr/order/$trackingNumber/detail");

                if ($response->successful()) {
                    $data = $response->json();

                    $totalTax = ceil($data['dist'][0]['totalTax'] ?? 0);
                    $totalCharges = ceil($data['dist'][0]['totalCharges'] ?? 0);

                    $courierCharges = $totalCharges + $totalTax;

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

                        foreach( $items as $item ){
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

                $helper = new LeopardApiHelper();
                $helper->parcelDelivered($detail);
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
            if ($status['label'] == 'Returned' && $detail->status != '9') {

                $response = Http::withHeaders([
                    'token' => $this->token,
                ])->get($this->url . "/payment/cpr/order/$trackingNumber/detail");

                if ($response->successful()) {
                    $data = $response->json();

                    $totalTax = ceil($data['dist'][0]['totalTax'] ?? 0);
                    $totalCharges = ceil($data['dist'][0]['totalCharges'] ?? 0);

                    $courierCharges = $totalCharges + $totalTax;

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

                        foreach( $items as $item ){
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

                $helper = new LeopardApiHelper();
                $dropshipper = DropShipper::where('user_id', $detail->belongs_to)->first();
                $shop = DropShipperShop::where('id', $detail->shop_id)->first();
                $helper->parcelCancel($dropshipper, $shop, $detail);

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

                //Check if balance is negative or not
                $totalProfit = Order::where('belongs_to', $detail->belongs_to)->sum('total_profit');
                $totalPaidProfit = Order::where('belongs_to', $detail->belongs_to)->sum('total_paid_profit');

                $remainingProfit = $totalProfit - $totalPaidProfit;
                if( $remainingProfit < 0 ){
                    $link = env('MIX_WEB_URL').'new-ticket';

                    NotificationHelper::addNotification(
                        $title = 'Account Deactivated - Negative Balance',
                        $messge = "Your account is deactivated due to negative balance of PKR $remainingProfit. Raise a ticket to reactivate. You can sign in but cannot place orders.",
                        $link = $link,
                        $image = null,
                        $directImage = null,
                        $color    = 'red',
                        $isPublic = 1,
                        $user = $detail->belongs_to
                    );
                }
            }

            //out for delivery
            if ($status['label'] == 'Out For Delivery') {
                $detail->update([
                    'status' => '11'
                ]);
            }

            if ($status['postex'] == 'Delivery Under Review') {
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

            OrderLeopardStatus::updateOrCreate(
                [
                    'order_id'   => $detail->id,       // Condition 1: order_id must match
                    'leopard_label' => $status['postex'],  // Condition 2: short_code must match
                ],
                [
                    'leopard_label'  => $status['postex'],
                    "short_code"     => 0,
                    'internal_label' => "",
                    'receiver_name'  => "",
                    'reason'         => "",
                ]
            );
        }
    }

    public function testBookAPacket($order, $order_no, $shop)
    {

        $city = City::where('id', $order->city_id)->first();
        $orderItems = OrderItem::with('variation.product')->where('order_id', $order->id)->get();

        $description = $orderItems->map(function ($item) {
            $productName = $item->variation->product->title ?? 'Product';
            $sku = $item->variation->sku ?? 'SKU';
            $qty = $item->quantity ?? 1;
            return "{$qty}x {$productName} ({$sku})";
        })->implode(', ');

        if (strlen($description) >= 500) {
            $description = $orderItems->map(function ($item) {
                $sku = $item->variation->sku ?? 'SKU';
                $qty = $item->quantity ?? 1;
                return "{$qty}x ({$sku})";
            })->implode(', ');
        }

        $shop = DropShipperShop::with('dropshipper')->where('id', $shop)->first();

        $storeCode = substr($shop->store_name, 0, 3) . '-' . $shop->id;
        $shipperCode = substr($shop->dropshipper->full_name, 0, 3) . '-' . substr($shop->store_name, 0, 3) . '-' . $shop->id;

        if (!$shop->postex_store_code) {
            $dropshipper = $this->createShipperAccount($shop);

            $data = $dropshipper->getData(); // returns stdClass
            if ($data->error  && $data->error  != '') {
                return $data = [
                    'error'  =>  $data->message,
                ];
            }
        }
        $order_no = $shop ?  substr($shop->dropshipper->full_name, 0, 3) . '-' . substr($shop->store_name, 0, 3) . '-' . $order_no : $order_no;
        $courierDisclaimer = CourierDisclaimer::where('courier_id', $order->courier_service_id)->first();
        $instruction = $order->instructions ? ($order->instructions . ', Dislaimer : ' . $courierDisclaimer->disclaimer) : ('Dislaimer : ' . $courierDisclaimer->disclaimer ?? "");

       $response = Http::withHeaders([
            'token' => $this->token,
        ])->post($this->url . '/order/create', [
            'customerName'       => $order->customer_name,
            'customerPhone'      => $order->phone_number,
            'deliveryAddress'    => $order->address,
            'invoicePayment'     => $order->selling_price,
            'orderDetail'        => $description,
            'orderRefNumber'     => $order_no,
            'returnAddressCode'  => $shipperCode,
            'cityName'           => $city->name,
            'items'              => $orderItems->sum('quantity'),
            'orderType'          => 'Normal', // 'Normal', 'Reverse', or 'Overland'
            'remarks'            => $instruction,
            'shipperCode'        => $shipperCode,
            'storeCode'          => $storeCode,
            'transactionNotes'   => $instruction,
        ]);

        return $data = [
            'data' => [
                'customerName'       => $order->customer_name,
                'customerPhone'      => $order->phone_number,
                'deliveryAddress'    => $order->address,
                'invoicePayment'     => $order->selling_price,
                'orderDetail'        => $description,
                'orderRefNumber'     => $order_no,
                'returnAddressCode'  => $shipperCode,
                'cityName'           => $city->name,
                'items'              => $orderItems->sum('quantity'),
                'orderType'          => 'Normal', // 'Normal', 'Reverse', or 'Overland'
                'remarks'            => $instruction,
                'shipperCode'        => $shipperCode,
                'storeCode'          => $storeCode,
                'transactionNotes'   => $instruction,
            ],
            'postEx' => $response
        ];

        return;
    }
}
