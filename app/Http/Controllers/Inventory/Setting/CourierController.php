<?php

namespace App\Http\Controllers\Inventory\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Courier\Courier;
use App\Models\Inventory\Courier\CourierAddedCategory;
use App\Models\Inventory\Courier\CourierCategory;
use App\Models\Inventory\Courier\CourierCategoryRange;
use App\Models\Inventory\Courier\CourierDisclaimer;
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
        $couriers = Courier::get();  // Get all couriers

        return response()->json([
            'status' => 'success',
            'response' => $couriers
        ], 200);
    }

    public function details( Request $request ){
        $courier = Courier::with('categories.ranges', 'disclaimer')
        ->where('id', $request->id)
        ->get();

        foreach ($courier as $item) {
            $item['disclaimer_text'] = $item->disclaimer->disclaimer ?? "";
        }

        return (new ResponseCollection($courier))
        ->response()
        ->setStatusCode(200);
    }

    /**
     * Store a newly created courier.
     */
    public function store(Request $request)
    {
        $lock = Cache::lock('add_courier_category')->block(7, function () use ($request) {
            // Validate the input data
            $validatedData = \Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'contactPerson' => 'required|string|max:255',
                'contactPersonNumber' => 'required|string|max:11',
            ]);

            $validation = $this->validation($validatedData);
            if ($validation) {
                return $validation;
            }

            $exists = Courier::where('courier_name', $request->name)->first();
            if ($exists) {
                return (new ValidationCollection(['Courier with this name already exist, please check record']))
                    ->response()
                    ->setStatusCode(421);
            }

            DB::transaction(function () use ($request) {
                // Create new courier
                $courier = Courier::create([
                    'courier_name'           => $request->name,
                    'contact_person'         => $request->contactPerson,
                    'contact_person_contact' => $request->contactPersonNumber,
                    'added_by'             => auth()->user()->id
                ]);
            });

            return response()->json([
                'status' => 'success'
            ], 201);
        });

        return $lock;
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

    public function addDisclaimer( Request $request ){

        CourierDisclaimer::updateOrInsert(
            ['courier_id' => $request->courier], // condition
            [
                'courier_id' => $request->courier,
                'disclaimer' => $request->text
            ]
        );

        return ['message' => 'Successfully added'];
    }

    public function updateRange( Request $request ){
        CourierCategoryRange::where('id', $request->id)->update([
                'minimum_quantity'  => $request->minimum_quantity,
                'maximum_quantity'  => $request->maximum_quantity,
                'base_rate'         => $request->base_rate,
                'per_kg'            => $request->per_kg,
                'per_kg_rate'       => $request->per_kg_rate,
                'fac_tax'           => $request->fac_tax,
                'gst_tax'           => $request->gst_tax,
                'total'             => $request->total,
        ]);

        return ['message' => 'Successfully added'];
    }

  public function changeStatus(Request $request)
    {
        $courier = Courier::find($request->id);

        if ($courier) {
            $courier->is_active = $courier->is_active ? 0 : 1;
            $courier->save();

            return ['message' => 'Successfully added'];
        }

    }

    public function fetchCategory(){
        $categories = CourierCategory::select('id as code', 'name as label')->get();
        return (new ResponseCollection($categories))
        ->response()
        ->setStatusCode(200);
    }

    public function fetchCategoryRanges( Request $request ){

        $categories = CourierCategoryRange::where('category_id', $request->category['code'])->get();
        return (new ResponseCollection($categories))
        ->response()
        ->setStatusCode(200);
    }

    public function addCategory( Request $request ){

        $lock = Cache::lock('add_courier_category')->block(7, function () use ($request) {
            $validatedData = \Validator::make($request->all(), [
                'name'          => 'required|string|max:255',
                'internalLabel' => 'required|string|max:255',
                'ranges'        => 'required|array|min:1',
                'ranges.*.minimum_quantity' => 'required|numeric|min:0',
                'ranges.*.maximum_quantity' => 'required|numeric|gte:ranges.*.minimum_quantity',
                'ranges.*.base_rate'        => 'required|numeric|min:0',
                'ranges.*.per_kg'           => 'nullable|numeric|min:0',  // per_kg can be null
                'ranges.*.per_kg_rate'      => 'nullable|numeric|min:0',  // per_kg_rate can be null
                'ranges.*.fc'               => 'required|numeric|between:0,100',  // FC tax as a percentage
                'ranges.*.gst'              => 'required|numeric|between:0,100',  // GST tax as a percentage
            ]);

            $validation = $this->validation($validatedData);
            if ($validation) {
                return $validation;
            }

            $exists = CourierCategory::where('name', $request->name)->first();
            if ($exists) {
                return (new ValidationCollection(['Courier category with name already exist, please check record']))
                    ->response()
                    ->setStatusCode(421);
            }

            DB::transaction(function () use ($request) {
                // Create the Courier Category
                $category = CourierCategory::create([
                    'courier_id'     => $request->input('id'),
                    'name'           => $request->input('name'),
                    'internal_label' => $request->input('internalLabel'),
                    'our_charges'    => $request->input('ourCharges') ?? 0,
                    'description'    => $request->input('description') ?? '', // Assuming there's a description
                    'added_by'       => auth()->user()->id,
                ]);

                // Loop through the ranges and create the related CourierCategoryRange records
                foreach ($request->input('ranges') as $range) {
                    CourierCategoryRange::create([
                        'our_charges'    => $request->input('ourCharges') ?? 0,
                        'category_id'      => $category->id,
                        'minimum_quantity' => $range['minimum_quantity'],
                        'maximum_quantity' => $range['maximum_quantity'],
                        'base_rate'        => $range['base_rate'],
                        'per_kg'           => $range['per_kg'],
                        'per_kg_rate'      => $range['per_kg_rate'],
                        'fac_tax'          => $range['fc'], // Assuming 'fc' corresponds to fac_tax
                        'gst_tax'          => $range['gst'],
                        'total'            => $this->calculateTotal($range), // Assuming you want to calculate the total
                        'added_by'         => auth()->user()->id,
                    ]);
                }
            });

            return ['message' => 'Successfully added'];
        });

        return $lock;
    }

    private function calculateTotal($range){
        $baseAmount = (float) $range['base_rate'] + ( (float)$range['per_kg_rate'] ?? 0);

        // Calculate FC and GST tax
        $fcTax = ($range['fc'] / 100) * $baseAmount;
        $gstTax = ($range['gst'] / 100) * ($baseAmount + $fcTax);

        // Return the total
        return round($baseAmount + $fcTax + $gstTax);
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
