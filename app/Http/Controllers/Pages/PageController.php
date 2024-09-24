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
        $data = HomePageSetting::get();

        return (new ResponseCollection( $data ))
            ->response()
            ->setStatusCode(200);
    }

    public function homePageSettingStore( Request $request ){

        if ($request->has('image') && !empty($request->image)) {
            foreach ($request->image as $image) {
                if (isset($image['link']) && isset($image['button_link']) && !empty($image['button_link'])) {
                    // Check if an image record exists, update or create new
                    $homePageSetting = HomePageSetting::where('type', 'image-' . $image['index'])->first();

                    if ($homePageSetting) {

                        $updateData = [
                            'position' => $image['button_link'], // Button link
                            'label' => $image['button_label'],   // Button label
                            'added_by' => auth()->user()->id,
                        ];

                        // Only update 'attachment' if 'link' is provided
                        if (!empty($image['link']) && $image['link'] != 'undefined') {
                            $updateData['attachment'] = $this->homeBanner($image['link']); // Attachment as image
                        }

                        // Perform the update
                        $homePageSetting->update($updateData);
                    } else {
                        // Create new image setting
                        HomePageSetting::create([
                            'type' => 'image-' . $image['index'],
                            'attachment' => $this->homeBanner($image['link']), // Button link as image attachment
                            'position' => $image['button_link'], // Position field used as button link
                            'label' => $image['button_label'], // Position field used as button link
                            'added_by' => auth()->user()->id,
                        ]);
                    }
                }
            }
        }

        if ( !empty($request->head_line)) {
            // Check if an image record exists, update or create new
            $homePageSetting = HomePageSetting::where('type', 'headline')->first();

            if ($homePageSetting) {
                // Update existing image settings
                $homePageSetting->update([
                    'attachment' => '', // Button link as image attachment
                    'position' => $request->head_line, // Position field used as button link
                    'added_by' => auth()->user()->id,
                ]);
            } else {

                // Create new image setting
                HomePageSetting::create([
                    'type' => 'headline',
                    'attachment' => '', // Button link as image attachment
                    'position' => $request->head_line, // Position field used as button link
                    'added_by' => auth()->user()->id,
                ]);
            }
        }

        // Handle Tags Logic
        if ($request->has('tags') && is_array($request->tags)) {
            foreach ($request->tags as $tag) {
                if($tag['link'] == 0){
                    continue;
                }
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
