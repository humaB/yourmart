<?php

namespace App\Http\Controllers\Inventory\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Setting\ShippingClass;
use App\Models\Inventory\Product\Setting\ShippingClassRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductShippingClassController extends Controller
{
    public function index()
    {
        return view('inventory.product.setting.shipping_classes');
    }

    public function fetchRecord()
    {
        $record = ShippingClass::with('user')->orderBy('id', 'desc')->get();
        return (new ResponseCollection($record))
            ->response()
            ->setStatusCode(200);
    }

    public function dropDown()
    {
        $record = ShippingClass::orderBy('id', 'desc')->select('id as code', 'name as label')->get();
        return (new ResponseCollection($record))
            ->response()
            ->setStatusCode(200);
    }

    public function details(Request $request)
    {
        $record = ShippingClassRate::where('shipping_class_id', $request->id)->get();

        return (new ResponseCollection($record))
            ->response()
            ->setStatusCode(200);
    }

    public function editDetails(Request $request)
    {

        $record = ShippingClass::with('details')->where('id', $request->id)->get();

        return (new ResponseCollection($record))
            ->response()
            ->setStatusCode(200);
    }

    public function changeStatus(Request $request)
    {

        ShippingClass::where('id', $request->id)->update([
            'is_active' => $request->status
        ]);

        return response()->json(['message' => 'Shipping class status updated successfully'], 200);
    }

    public function store(Request $request)
    {

        $lock = Cache::lock('shipping_class_store')->block(7, function () use ($request) {

            $validatedData = \Validator::make($request->all(), [
                'name'          => 'required|string|max:255',
                'description'   => 'nullable|string',
                'rate_type'     => 'required',
                'minimum_order' => 'nullable|numeric|min:0',
                'flat_rate'     => 'nullable|numeric|min:0',
                'base_rate'     => 'nullable|numeric|min:0',
                'rate_per_unit' => 'nullable|numeric|min:0',
            ], [
                'name.required' => 'Please provide a name for the shipping class.',
                'name.string' => 'The name must be a valid string.',
                'name.max' => 'The name must not exceed 255 characters.',
                'description.string' => 'The description must be a valid string.',
                'rate_type.required' => 'Please select a rate method for the shipping class.',
                'minimum_order.numeric' => 'The minimum order quantity must be a number.',
                'minimum_order.min' => 'The minimum order quantity cannot be less than 0.',
                'flat_rate.numeric' => 'The flat rate must be a number.',
                'flat_rate.min' => 'The flat rate cannot be less than 0.',
                'base_rate.numeric' => 'The base rate must be a number.',
                'base_rate.min' => 'The base rate cannot be less than 0.',
                'rate_per_unit.numeric' => 'The rate per unit must be a number.',
                'rate_per_unit.min' => 'The rate per unit cannot be less than 0.',
            ]);

            $validation = $this->validation($validatedData);
            if ($validation) {
                return $validation;
            }

            $exists = ShippingClass::where('name', $request->name)->first();
            if ($exists) {
                return (new ValidationCollection(['Shipping with class name already exist, please check record']))
                    ->response()
                    ->setStatusCode(421);
            }

            // Create Shipping Class
            $shipping = ShippingClass::create([
                'name' => $request->input('name'),
                'description' => $request->input('description', null),
                'added_by' => auth()->user()->id,
            ]);

            // Create Shipping Class Rate
            ShippingClassRate::create([
                'shipping_class_id' => $shipping->id,
                'rate_type' => $request->input('rate_type'),
                'minimum_order' => $request->input('minimum_order', null),
                'flat_rate' => $request->input('rate', null) ?? $request->input('flat_rate', null),
                'base_rate' => $request->input('base_rate', null),
                'rate_per_unit' => $request->input('rate_per_unit', null),
                'added_by' => auth()->user()->id,
            ]);

            // Return a success response or redirect
            return response()->json(['message' => 'Shipping class created successfully'], 201);
        });

        return $lock;
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $lock = Cache::lock('shipping_class_update_' . $request->id)->block(7, function () use ($request, $id) {
            // Validate the incoming request data
            $validatedData = \Validator::make($request->all(), [
                'name'          => 'required|string|max:255',
                'description'   => 'nullable|string',
                'rate_type'     => 'required',
                'minimum_order' => 'nullable|numeric|min:0',
                'flat_rate'     => 'nullable|numeric|min:0',
                'base_rate'     => 'nullable|numeric|min:0',
                'rate_per_unit' => 'nullable|numeric|min:0',
            ], [
                'name.required' => 'Please provide a name for the shipping class.',
                'name.string' => 'The name must be a valid string.',
                'name.max' => 'The name must not exceed 255 characters.',
                'description.string' => 'The description must be a valid string.',
                'rate_type.required' => 'Please select a rate method for the shipping class.',
                'minimum_order.numeric' => 'The minimum order quantity must be a number.',
                'minimum_order.min' => 'The minimum order quantity cannot be less than 0.',
                'flat_rate.numeric' => 'The flat rate must be a number.',
                'flat_rate.min' => 'The flat rate cannot be less than 0.',
                'base_rate.numeric' => 'The base rate must be a number.',
                'base_rate.min' => 'The base rate cannot be less than 0.',
                'rate_per_unit.numeric' => 'The rate per unit must be a number.',
                'rate_per_unit.min' => 'The rate per unit cannot be less than 0.',
            ]);

            $validation = $this->validation($validatedData);
            if ($validation) {
                return $validation;
            }

            // Find existing ShippingClass
            $shippingClass = ShippingClass::find($id);
            if (!$shippingClass) {
                return (new ValidationCollection(['Shipping class not found']))
                ->response()
                ->setStatusCode(404);
            }

            // Check if name already exists (excluding the current record)
            $exists = ShippingClass::where('name', $request->input('name'))
                ->where('id', '!=', $id)
                ->first();
            if ($exists) {
                return (new ValidationCollection(['Shipping class name already exists']))
                ->response()
                ->setStatusCode(421);
            }

            // Update Shipping Class
            $shippingClass->update([
                'name'        => $request->input('name'),
                'description' => $request->input('description', null),
            ]);

            // Find or create Shipping Class Rate
            $shippingClassRate = ShippingClassRate::where('shipping_class_id', $id)->first();
            if (!$shippingClassRate) {
                return (new ValidationCollection(['Shipping class rate not found']))
                ->response()
                ->setStatusCode(404);
            }

            // Update Shipping Class Rate
            $shippingClassRate->update([
                'rate_type'     => $request->input('rate_type'),
                'minimum_order' => $request->input('minimum_order', null),
                'flat_rate'     => $request->input('rate', null) ?? $request->input('flat_rate', null),
                'base_rate'     => $request->input('base_rate', null),
                'rate_per_unit' => $request->input('rate_per_unit', null),
            ]);

            // Return a success response
            return response()->json(['message' => 'Shipping class updated successfully'], 200);
        });

        return $lock;
    }

    private function validation($validator)
    {

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
