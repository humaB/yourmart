<?php

namespace App\Http\Controllers\Inventory\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Setting\MinimumOrderQuantity;
use Illuminate\Http\Request;

class ProductMinimumOrderController extends Controller
{
    public function index(){
        return view('inventory.product.setting.minimum_order_quantity');
    }

    public function fetchHistory(){
        $history = MinimumOrderQuantity::with('user')->orderBy('id', 'desc')->get();
        return ( new ResponseCollection ( $history  ) )
        ->response()
        ->setStatusCode( 200 );
    }

    public function store( Request $request ){

          // Validate request
        $validator = \Validator::make($request->all(), [
            'guest' => 'nullable|integer|min:0',
            'registered' => 'nullable|integer|min:0',
        ], [
            'guest.integer' => 'Guest quantity must be a valid integer.',
            'registered.integer' => 'Registered quantity must be a valid integer.',
            'guest.min' => 'Guest quantity must be at least 0.',
            'registered.min' => 'Registered quantity must be at least 0.',
        ]);

        $validation = $this->validation($validator);
        if ($validation) {
            return $validation;
        }

        $userId = auth()->user()->id;

        // Manage guest quantity
        if ($request->filled('guest') && $request->guest != 0 ) {
            // Set existing guest records to inactive
            MinimumOrderQuantity::where('type', 0)
                ->where('status', 0) // Only active records
                ->update(['status' => 1]);

            // Create new guest record
            MinimumOrderQuantity::create([
                'status' => 0, // Active
                'type' => 0,   // Guest
                'quantity' => $request->input('guest'),
                'added_by' => $userId
            ]);
        }

        // Manage registered quantity
        if ($request->filled('registered') && $request->registered != 0 ) {
            // Set existing registered records to inactive
            MinimumOrderQuantity::where('type', 1)
                ->where('status', 0) // Only active records
                ->update(['status' => 1]);

            // Create new registered record
            MinimumOrderQuantity::create([
                'status' => 0, // Active
                'type' => 1,   // Registered
                'quantity' => $request->input('registered'),
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
