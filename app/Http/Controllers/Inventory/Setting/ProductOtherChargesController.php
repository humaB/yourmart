<?php

namespace App\Http\Controllers\Inventory\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Setting\OtherCharge;
use Illuminate\Http\Request;

class ProductOtherChargesController extends Controller
{
    public function index(){
        return view('inventory.product.setting.other_charges');
    }

    public function fetchHistory(){

        $history = OtherCharge::with('user')->orderBy('id', 'desc')->get();

        return ( new ResponseCollection ( $history  ) )
        ->response()
        ->setStatusCode( 200 );
    }

    public function store( Request $request ){

          // Validate request
        $validator = \Validator::make($request->all(), [
            'darazPacking' => 'nullable|integer|min:0',
            'returnCharges' => 'nullable|integer|min:0',
        ]);

        $validation = $this->validation($validator);
        if ($validation) {
            return $validation;
        }

        $userId = auth()->user()->id;

        // Manage guest quantity
        if ($request->filled('darazPacking')  ) {
            // Set existing guest records to inactive
            OtherCharge::where('type', 'Daraz')
                ->where('status', 0) // Only active records
                ->update(['status' => 1]);

            // Create new guest record
            OtherCharge::create([
                'status' => 0, // Active
                'type' => 'Daraz',   // Guest
                'amount' => $request->input('darazPacking'),
                'added_by' => $userId
            ]);
        }

        // Manage registered quantity
        if ($request->filled('returnCharges') ) {
            // Set existing registered records to inactive
            OtherCharge::where('type', 'Return')
            ->where('status', 0) // Only active records
            ->update(['status' => 1]);

            // Create new guest record
            OtherCharge::create([
                'status' => 0, // Active
                'type' => 'Return',   // Guest
                'amount' => $request->input('returnCharges'),
                'added_by' => $userId
            ]);
        }

        return ['message' => 'Updated Successfully'];

    }

    private function validation($validator){

        if ($validator->fails()) {

            $validationErrors = [];
            $errors = $validator->errors()->all();

            foreach ($errors as $error) {
                array_push($validationErrors, $error);
            }

            return (new ValidationCollection($validationErrors))
                ->response()
                ->setStatusCode(400);
        }
    }
}
