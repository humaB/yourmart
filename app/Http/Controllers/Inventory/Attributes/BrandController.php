<?php

namespace App\Http\Controllers\Inventory\Attributes;

use App\Helpers\SlugHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BrandController extends Controller
{
    public function fetchBrand(){

        $record = Brand::with('user')->orderBy('id', 'desc')->get();

        $data = [
            'dropdown' => $record->map(function ($item) {
                return [
                    'code' => $item->id,
                    'label' => $item->name
                ];
            }),
            'record'   => $record
        ];

        return ( new ResponseCollection ( $data  ) )
        ->response()
        ->setStatusCode( 200 );
    }

    public function store( Request $request ){
        $lock = Cache::lock('add_brand')->block(7, function () use ($request) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validates if image is present
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = Brand::where('name', $request->name)->first();
            if( $exist ){
                return (new ValidationCollection(['This brand name is already added']))
                ->response()
                ->setStatusCode(421);
            }

            $userId = auth()->user()->id;

            Brand::create([
                'name'         => $request->name,
                'slug'         => SlugHelper::generateSlug($request->name),
                'description'  => $request->description,
                'logo'         => $request->image ? $this->logo( $request->image) : "",
                'added_by'     => $userId
            ]);

            return response()->json(['message' => 'Brand added successfully'], 201);
        });

        return $lock;

    }

    public function update(Request $request) {
        $id = $request->id;
        $lock = Cache::lock('update_brand_' . $id)->block(7, function () use ($request, $id) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048|required_with:other_field',
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = Brand::where('name', $request->name)->where('id', '!=', $id)->first();
            if ($exist) {
                return (new ValidationCollection(['This brand name is already added']))
                    ->response()
                    ->setStatusCode(421);
            }

            $brand = Brand::find($id);
            if (!$brand) {
                return (new ValidationCollection(['Brand not found']))
                ->response()
                ->setStatusCode(404);
            }

            $userId = auth()->user()->id;


            $data = [
                'name' => $request->name,
                'slug' => SlugHelper::generateSlug($request->name),
                'description' => $request->description,
                'added_by' => $userId
            ];

            if ($request->hasFile('image')) {
                $data['logo'] = $this->logo($request->image);
            }

            $brand->update($data);

            return response()->json(['message' => 'Brand updated successfully'], 200);
        });

        return $lock;
    }

    public function logo( $image  ){
        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '' ,$filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path            = $image->storeAs('public/uploads/inventory/brands/', $nameToStore);
        return $nameToStore;
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
