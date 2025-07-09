<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\NotificationHelper;
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
        $setting = HelpCenterPageSetting::create([
            'name' => $request->name,
            'description' => $request->description,  // Store the image path if available
            'type' => $request->type,  // Store the image path if available
            'added_by' => auth()->user()->id, // Assuming you're using authentication
        ]);

        $link = env('MIX_WEB_URL').'help-center';

        $type = strtolower($request->type); // e.g., 'user guidline', 'faq', 'policy'
        $name = $request->name;             // This is the title

        switch ($type) {
            case 'user guidline':
                $title = 'New Guideline Added';
                $message = "Check out the latest update: $name";
                break;

            case 'faq':
                $title = 'New FAQ Added';
                $message = "Find answers faster: $name";
                break;

            case 'policy':
                $title = 'New Policy Added';
                $message = "Please review: $name";
                break;
        }

        NotificationHelper::addNotification(
            $title,
            $message,
            $link,
            $image = null,
            $directImage = null,
            $color = 'blue',
            $isPublic = 0,
            $user = null
        );


        return response()->json(['message' => 'Help Center page settings saved successfully!'], 200);
    }

    public function helpCenterPageSettingUpdate( Request $request )
    {
        HelpCenterPageSetting::where("id",$request->id)->update([
            'name' => $request->name,
            'description' => $request->description,  // Store the image path if available
            'type' => $request->type,  // Store the image path if available
        ]);

        $link = env('MIX_WEB_URL').'help-center';

        $type = strtolower($request->type); // e.g., 'user guidline', 'faq', 'policy'
        $name = $request->name;             // This is the title

        switch ($type) {
            case 'user guidline':
                $title = 'New Guideline Added';
                $message = "Check out the latest update: $name";
                break;

            case 'faq':
                $title = 'New FAQ Added';
                $message = "Find answers faster: $name";
                break;

            case 'policy':
                $title = 'New Policy Added';
                $message = "Please review: $name";
                break;
        }

        NotificationHelper::addNotification(
            $title,
            $message,
            $link,
            $image = null,
            $directImage = null,
            $color = 'blue',
            $isPublic = 0,
            $user = null
        );

        return response()->json(['message' => 'Help Center page settings saved successfully!'], 200);
    }
}
