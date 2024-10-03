<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
}
