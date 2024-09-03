<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Helpers\SlugHelper;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\AttributeType;

class AttributeController extends Controller
{
    public function fetchAttributes(){

        $record = AttributeType::with('user', 'parent:id,name')->orderBy('id', 'desc')->get();
        $parent = AttributeType::with('user', 'parent:id,name')->where('parent_id', '0')->select('id as code', 'name as label')->get();
        $categories = AttributeType::with('user', 'parent:id,name')->where('parent_id', '!=' ,'0')->select('id as code', 'name as label')->get();

        $data = [
            'dropdown' => $categories,
            'parent'   => $parent,
            'record'   => $record
        ];

        return ( new ResponseCollection ( $data  ) )
        ->response()
        ->setStatusCode( 200 );
    }

    public function store( Request $request ){
        $lock = Cache::lock('add_attribute')->block(7, function () use ($request) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = AttributeType::where('name', $request->name)->first();
            if( $exist ){
                return (new ValidationCollection(['This category name is alread added']))
                ->response()
                ->setStatusCode(421);
            }

            $userId = auth()->user()->id;

            AttributeType::create([
                'name'         => $request->name,
                'slug'         => SlugHelper::generateSlug($request->name),
                'description'  => $request->description,
                'parent_id'    => $request->parent ?? 0,
                'added_by'     => $userId
            ]);

            return response()->json(['message' => 'attribute added successfully'], 201);
        });

        return $lock;

    }

    public function update(Request $request) {
        $id = $request->id;
        $lock = Cache::lock('update_attribute_' . $id)->block(7, function () use ($request, $id) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'name' => 'required',
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $exist = AttributeType::where('name', $request->name)
            ->where('parent_id', $request->parent_id) // or wherever you get the parent ID
            ->where('id', '!=', $id)
            ->first();
            if ($exist) {
                return (new ValidationCollection(['This attribute name is already added']))
                    ->response()
                    ->setStatusCode(421);
            }

            $attribute = AttributeType::find($id);
            if (!$attribute) {
                return (new ValidationCollection(['Attribute not found']))
                ->response()
                ->setStatusCode(404);
            }

            $userId = auth()->user()->id;

            $data = [
                'name' => $request->name,
                'slug' => SlugHelper::generateSlug($request->name),
                'parent_id' => $request->parent,
                'description' => $request->description,
                'added_by' => $userId
            ];

            $attribute->update($data);

            return response()->json(['message' => 'attribute updated successfully'], 200);
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
