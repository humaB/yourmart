<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Setting\HomePageSetting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index(){
        return view('pages.page_index');
    }

    public function fectHomePageSettingStore(){
        $data = HomePageSetting::where('type', 'tag')->get();

        return (new ResponseCollection( $data ))
            ->response()
            ->setStatusCode(200);
    }

    public function homePageSettingStore( Request $request ){
           // Handle Image Logic (button_link)
           if ($request->has('image_link') && $request->has('button_link') && !empty($request->button_link)) {
            // Check if an image record exists, update or create new
            $homePageSetting = HomePageSetting::where('type', 'image')->first();

            if ($homePageSetting) {
                // Update existing image settings
                $homePageSetting->update([
                    'attachment' => $this->homeBanner( $request->image_link), // Button link as image attachment
                    'position' => $request->button_link, // Position field used as button link
                    'added_by' => auth()->user()->id,
                ]);
            } else {
                // Create new image setting
                HomePageSetting::create([
                    'type' => 'image',
                   'attachment' => $this->homeBanner( $request->image_link), // Button link as image attachment
                    'position' => $request->button_link, // Position field used as button link
                    'added_by' => auth()->user()->id,
                ]);
            }
        }

        // Handle Tags Logic
        if ($request->has('tags') && is_array($request->tags)) {
            foreach ($request->tags as $tag) {

                // Check if tag exists, then update or create
                $homePageSetting = HomePageSetting::where('type', 'tag')->where('tag_id', $tag['link'])->first();

                if ($homePageSetting) {
                    // Update existing tag settings
                    $homePageSetting->update([
                        'position' => $tag['position'], // Update position for the tag
                        'added_by' => auth()->user()->id,
                    ]);
                } else {
                    // Create new tag setting
                    HomePageSetting::create([
                        'type' => 'tag',
                        'tag_id' => $tag['link'],
                        'position' => $tag['position'],
                        'added_by' => auth()->user()->id,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Home page settings saved successfully!'], 200);
    }

    public function homeBanner( $image  ){
        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '' ,$filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path            = $image->storeAs('public/uploads/pages/home/banners', $nameToStore);
        return $nameToStore;
    }
}
