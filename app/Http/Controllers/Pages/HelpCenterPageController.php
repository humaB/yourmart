<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Setting\HomePageSetting;
use App\Models\Setting\LibraryPageSetting;
use App\Models\Setting\HelpCenterPageSetting;
use Illuminate\Http\Request;

class HelpCenterPageController extends Controller
{
    public function index(){
        return view('pages.help_center_page');
    }

    public function fectHelpCenterPageSetting(){
        $data = HelpCenterPageSetting::with("added_name")->get();

        return (new ResponseCollection( $data ))
            ->response()
            ->setStatusCode(200);
    }

    public function helpCenterPageSettingStore( Request $request ){

        // Create the record in the page_library_contents table
        HelpCenterPageSetting::create([
            'name' => $request->name,
            'description' => $request->description,  // Store the image path if available
            'type' => $request->type,  // Store the image path if available
            'added_by' => auth()->user()->id, // Assuming you're using authentication
        ]);

        return response()->json(['message' => 'Help Center page settings saved successfully!'], 200);
    }

    public function helpCenterPageSettingUpdate( Request $request )
    {
        HelpCenterPageSetting::where("id",$request->id)->update([
            'name' => $request->name,
            'description' => $request->description,  // Store the image path if available
            'type' => $request->type,  // Store the image path if available
        ]);

        return response()->json(['message' => 'Help Center page settings saved successfully!'], 200);
    }
}
