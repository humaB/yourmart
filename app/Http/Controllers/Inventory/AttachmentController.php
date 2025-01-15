<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\ValidationCollection;
use App\Models\Inventory\Product\ProductAttachment;
use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariationImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

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
                        'title'        => $uploadedPath,
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

    public function update( Request $request ){
        $lock = Cache::lock('update_attachment')->block(7, function () use ($request) {

            ProductAttachment::where('id', $request->id)->update([
                'alt'         => $request->alt,
                'title'       => $request->title,
                'caption'     => $request->caption,
                'description' => $request->description,
            ]);

            return response()->json(['message' => 'Attachment updated successfully'], 200);
        });

        return $lock;
    }

    public function delete( Request $request ){
        $lock = Cache::lock('delete_attachment')->block(7, function () use ($request) {

            $product = Product::where('hero_image', $request->attachment)->first();
            $variation = ProductVariationImage::where('image_id', $request->id)->first();

            if( $product || $variation ){
                return (new ValidationCollection(['This image has been used with some product']))
                ->response()
                ->setStatusCode(421);
            }

            ProductAttachment::where('id', $request->id)->delete();

            return response()->json(['message' => 'Attachment updated successfully'], 200);
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

        // Prepare the file to send to another Laravel project
        $filePath = storage_path('app/' . $path);
        $response = Http::attach(
            'file',
            file_get_contents($filePath),
            $nameToStore
        )->post(env('MIX_WEB_URL') . 'public/api/upload-attachment');

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
