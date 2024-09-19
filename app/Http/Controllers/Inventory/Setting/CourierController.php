<?php

namespace App\Http\Controllers\Inventory\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Setting\ShippingClass;
use App\Models\Inventory\Product\Setting\ShippingClassRate;
use App\Models\Inventory\Product\Setting\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CourierController extends Controller
{
    public function index(Request $request)
    {
        return view('inventory.product.setting.couriers');
    }

    public function couriers()
    {
        $couriers = Courier::all();  // Get all couriers

        return response()->json([
            'status' => 'success',
            'response' => $couriers
        ], 200);
    }

    /**
     * Store a newly created courier.
     */
    public function store(Request $request)
    {
        // Validate the input data
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500'
        ]);

        // Create new courier
        $courier = Courier::create([
            'name' => $request->name,
            'contact' => $request->contact,
            'address' => $request->address
        ]);

        return response()->json([
            'status' => 'success',
            'response' => $courier
        ], 201);
    }

    /**
     * Update the specified courier.
     */
    public function update(Request $request)
    {
        // Validate the input data
        $request->validate([
            'id' => 'required|exists:couriers,id',  // Ensure courier exists
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500'
        ]);

        // Find the courier by ID
        $courier = Courier::findOrFail($request->id);

        // Update courier details
        $courier->update([
            'name' => $request->name,
            'contact' => $request->contact,
            'address' => $request->address
        ]);

        return response()->json([
            'status' => 'success',
            'response' => $courier
        ], 200);
    }
}