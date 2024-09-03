<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SizeController extends Controller
{
    public function fetchSizes(){

        $record = Size::with('user')->orderBy('id', 'desc')->get();

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
        $lock = Cache::lock('add_size')->block(7, function () use ($request) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = Size::where('name', $request->name)->first();
            if( $exist ){
                return (new ValidationCollection(['This color name is alread added']))
                ->response()
                ->setStatusCode(421);
            }

            $userId = auth()->user()->id;

            $baseCode = substr(strtoupper($request->name), 0, 4);
            $code = $baseCode;

            $counter = 1;
            while (Size::where('code', $code)->exists()) {
                $code = $baseCode . $counter;
                $counter++;
            }

            Size::create([
                'name'         => $request->name,
                'code'         => $code,
                'sort_by'      => '0',
                'added_by'     => $userId
            ]);

            return response()->json(['message' => 'Size added successfully'], 201);
        });

        return $lock;

    }

    public function update(Request $request) {
        $id = $request->id;
        $lock = Cache::lock('update_size_' . $id)->block(7, function () use ($request, $id) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = Size::where('name', $request->name)->where('id', '!=', $id)->first();
            if ($exist) {
                return (new ValidationCollection(['This Size name is already added']))
                    ->response()
                    ->setStatusCode(421);
            }

            $size = Size::find($id);
            if (!$size) {
                return (new ValidationCollection(['Size not found']))
                ->response()
                ->setStatusCode(404);
            }

            $userId = auth()->user()->id;

            $baseCode = substr(strtoupper($request->name), 0, 4);
            $code = $baseCode;

            $counter = 1;
            while (Size::where('code', $code)->exists()) {
                $code = $baseCode . $counter;
                $counter++;
            }

            $data = [
                'name'     => $request->name,
                'code'     => $code,
                'added_by' => $userId
            ];

            $size->update($data);

            return response()->json(['message' => 'Size updated successfully'], 200);
        });

        return $lock;
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
