<?php

namespace App\Http\Controllers\Helpers;

use App\Models\Inventory\Courier\CourierCategory;
use App\Models\User\DropShipperShop;
use Illuminate\Support\Facades\Http;

class LeopardApiHelper
{
    private $apiKey = '487F7B22F68312D2C1BBC93B1AEA445B1726751602';
    private $apiPassword = 'Allah@001#';

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
}
