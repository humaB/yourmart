<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Setting\HomePageSetting;
use App\Models\Setting\LibraryPageSetting;
use Illuminate\Http\Request;

class LibraryPageController extends Controller
{
    public function index(){
        return view('pages.library_page');
    }

    public function fectLibraryPageSettingStore(){
        $data = LibraryPageSetting::with("added_name")->get();

        return (new ResponseCollection( $data ))
            ->response()
            ->setStatusCode(200);
    }

    public function libraryPageSettingStore( Request $request ){

        // Decode the incoming JSON data
        $data = json_decode($request->data);

        // Create the record in the page_library_contents table
        LibraryPageSetting::create([
            'name' => $data->name,
            'description' => $data->description,
            'attachment' => $this->image($request->image),  // Store the image path if available
            'video_links' => $data->video_links,
            'added_by' => auth()->user()->id, // Assuming you're using authentication
        ]);

        return response()->json(['message' => 'Library page settings saved successfully!'], 200);
    }
    
    public function libraryPageSettingUpdate( Request $request )
    {
        // Decode the incoming JSON data
        $data = json_decode($request->data);

        // Create the record in the page_library_contents table
        LibraryPageSetting::where("id",$data->id)->update([
            'name' => $data->name,
            'description' => $data->description,
            'video_links' => $data->video_links,
            'added_by' => auth()->user()->id, // Assuming you're using authentication
        ]);

        if(isset($request->image))
        {
            LibraryPageSetting::where("id",$data->id)->update([
                'attachment' => $this->image($request->image),  // Store the image path if available
            ]);
            
        }

        return response()->json(['message' => 'Library page settings saved successfully!'], 200);
    }

    public function image( $image  ){
        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '' ,$filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path            = $image->storeAs('public/uploads/pages/library/courses', $nameToStore);
        return $nameToStore;
    }
}
