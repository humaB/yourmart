<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\NotificationHelper;
use App\Http\Resources\ResponseCollection;
use App\Models\Setting\HomePageSetting;
use App\Models\Setting\LibraryPageSetting;
use Illuminate\Http\Request;

class LibraryPageController extends Controller
{
    public function index(){
        return view('pages.library_page');
    }

    public function fetchCourses(){
        $data = LibraryPageSetting::with("added_name")->get();

        return (new ResponseCollection( $data ))
            ->response()
            ->setStatusCode(200);
    }

    public function store( Request $request ){

        // Decode the incoming JSON data
        $data = json_decode($request->data);

        // Create the record in the page_library_contents table
        $video = LibraryPageSetting::create([
            'name' => $data->name,
            'description' => $data->description,
            'attachment' => $this->image($request->image),  // Store the image path if available
            'video_links' => $data->video_links,
            'added_by' => auth()->user()->id, // Assuming you're using authentication
        ]);

        $link = env('MIX_WEB_URL').'courses/'.$video->id;
        foreach($data->video_links as $video){
            NotificationHelper::addNotification(
                $title = 'New Learning Video Added',
                $messge = "Watch in: $data->name",
                $link = $link,
                $image = null,
                $directImage = null,
                $color    = 'blue',
                $isPublic = 0,
                $user = null
            );
        }

        return response()->json(['message' => 'Library page settings saved successfully!'], 200);
    }

    public function update( Request $request )
    {
        // Decode the incoming JSON data
        $data = json_decode($request->data);

        // Create the record in the page_library_contents table
        $library = LibraryPageSetting::where("id",$data->id)->first();

        $newVideos = array_diff($data->video_links, $library->video_links);


        $library->update([
            'name'        => $data->name,
            'description' => $data->description,
            'video_links' => $data->video_links,
            'added_by'    => auth()->user()->id, // Assuming you're using authentication
        ]);


        if(isset($request->image))
        {
            LibraryPageSetting::where("id",$data->id)->update([
                'attachment' => $this->image($request->image),  // Store the image path if available
            ]);

        }

        $link = env('MIX_WEB_URL').'courses/'.$data->id;
        foreach($newVideos as $video){
            NotificationHelper::addNotification(
                $title = 'New Learning Video Added',
                $messge = "Watch in: $data->name",
                $link = $link,
                $image = null,
                $directImage = null,
                $color    = 'blue',
                $isPublic = 0,
                $user = null
            );
        }

        return response()->json(['message' => 'Library page settings saved successfully!'], 200);
    }

    public function delete( Request $request ){

        LibraryPageSetting::where('id', $request->id)->delete();
        return response()->json(['message' => 'Library page settings saved successfully!'], 200);
    }

    public function dropshipperIndex(){
        return view('pages.dropshipper_setting_page');
    }

    public function fetchDropshipperSetting(){
        $dropshipper = LibraryPageSetting::where('name', ['dropshipper-page'])->first();
        $supplier = LibraryPageSetting::where('name', ['supplier-page'])->first();

        $data = [
            'supplier'    => $supplier,
            'dropshipper' => $dropshipper
        ];
        return (new ResponseCollection( $data ))
            ->response()
            ->setStatusCode(200);
    }

    public function dropshipperSettingStore( Request $request ){

        $data = [
            'description' => $request->type . '-page',
            'video_links' => $request->video,
            'added_by'    => auth()->user()->id, // Assuming you're using authentication
        ];

        // Check if attachment is present in the request and add it to the data array
        if ($request->attachment && $request->has('attachment')) {
            $data['attachment'] = $this->image($request->attachment);
        }

        LibraryPageSetting::updateOrCreate(
            ['name' => $request->type . '-page'], // Condition to check for an existing record
            $data
        );

        return response()->json(['message' => 'DS/SP PAGE settings saved successfully!'], 200);
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
