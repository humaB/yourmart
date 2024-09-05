<?php

namespace App\Http\Controllers\Inventory\Attributes;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ColorController extends Controller
{
    public function fetchColors(){

        $record = Color::with('user')->orderBy('id', 'desc')->get();

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
        $lock = Cache::lock('add_color')->block(7, function () use ($request) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validates if image is present
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = Color::where('name', $request->name)->first();
            if( $exist ){
                return (new ValidationCollection(['This color name is already added']))
                ->response()
                ->setStatusCode(421);
            }

            $userId = auth()->user()->id;

            $baseCode = substr(strtoupper($request->name), 0, 4);
            $code = $baseCode;

            $counter = 1;
            while (Color::where('code', $code)->exists()) {
                $code = $baseCode . $counter;
                $counter++;
            }

            Color::create([
                'name'         => $request->name,
                'code'         => $code,
                'hex'          => $request->hex,
                'image'        => $request->image ? $this->logo( $request->image) : "",
                'added_by'     => $userId
            ]);

            return response()->json(['message' => 'Color added successfully'], 201);
        });

        return $lock;

    }

    public function update(Request $request) {
        $id = $request->id;
        $lock = Cache::lock('update_color_' . $id)->block(7, function () use ($request, $id) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validates if image is present
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = Color::where('name', $request->name)->where('id', '!=', $id)->first();
            if ($exist) {
                return (new ValidationCollection(['This Color name is already added']))
                    ->response()
                    ->setStatusCode(421);
            }

            $color = Color::find($id);
            if (!$color) {
                return (new ValidationCollection(['Color not found']))
                ->response()
                ->setStatusCode(404);
            }

            $userId = auth()->user()->id;

            $baseCode = substr(strtoupper($request->name), 0, 4);
            $code = $baseCode;

            $counter = 1;
            while (Color::where('code', $code)->exists()) {
                $code = $baseCode . $counter;
                $counter++;
            }

            $data = [
                'name'     => $request->name,
                'code'     => $code,
                'hex'      => $request->hex,
                'added_by' => $userId
            ];

            if ($request->hasFile('image')) {
                $data['image'] = $this->logo($request->image);
            }

            $color->update($data);

            return response()->json(['message' => 'Color updated successfully'], 200);
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
        $path            = $image->storeAs('public/uploads/inventory/colors/', $nameToStore);
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
