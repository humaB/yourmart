<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\ProductAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AttachmentController extends Controller
{
    public function fetchAttachments(){

        $data = ProductAttachment::orderBy('id', 'desc')->get();

        return ( new ResponseCollection ( $data  ) )
        ->response()
        ->setStatusCode( 200 );
    }

    public function store( Request $request ){
        $lock = Cache::lock('upload_attachment')->block(7, function () use ($request) {
            // Validate request
            $validator = \Validator::make($request->all(), [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:25000', // Validates if image is present
            ]);

            $validation = $this->validation($validator);
            if ($validation) {
                return $validation;
            }

            $userId = auth()->user()->id;

            // Get the uploaded images
            $images = $request->file('images');

            $uploadedPaths = [];
            if ($images && is_array($images)) {
                foreach ($images as $image) {
                    // Process and store each image
                    $uploadedPath = $this->image($image);

                    // Save the image information in the database
                    ProductAttachment::create([
                        'alt'          => $request->alt,
                        'attachment'   => $uploadedPath,
                        'added_by'     => $userId,
                    ]);

                    $uploadedPaths[] = $uploadedPath;
                }
            }

            return response()->json(['message' => 'Attachment Uploaded successfully'], 201);
        });

        return $lock;
    }

    public function image( $image  ){
        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '' ,$filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path            = $image->storeAs('public/uploads/inventory/products/media/', $nameToStore);
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
