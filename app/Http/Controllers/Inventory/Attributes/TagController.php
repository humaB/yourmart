<?php

namespace App\Http\Controllers\Inventory\Attributes;

use App\Helpers\SlugHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TagController extends Controller
{
    public function fetchTags(){

        $record = Tag::orderBy('id', 'desc')->get();

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
        $lock = Cache::lock('add_tag')->block(7, function () use ($request) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = Tag::where('name', $request->name)->first();
            if( $exist ){
                return (new ValidationCollection(['This tag name is already added']))
                ->response()
                ->setStatusCode(421);
            }

            $userId = auth()->user()->id;

            Tag::create([
                'name'         => $request->name,
                'slug'         => SlugHelper::generateSlug($request->name),
                'added_by'     => $userId
            ]);

            return response()->json(['message' => 'Tag added successfully'], 201);
        });

        return $lock;

    }

    public function update(Request $request) {
        $id = $request->id;
        $lock = Cache::lock('update_tag_' . $id)->block(7, function () use ($request, $id) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = Tag::where('name', $request->name)->where('id', '!=', $id)->first();
            if ($exist) {
                return (new ValidationCollection(['This Size name is already added']))
                    ->response()
                    ->setStatusCode(421);
            }

            $size = Tag::find($id);
            if (!$size) {
                return (new ValidationCollection(['Size not found']))
                ->response()
                ->setStatusCode(404);
            }

            $userId = auth()->user()->id;

            $data = [
                'name'     => $request->name,
                'slug'     => SlugHelper::generateSlug($request->name),
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
