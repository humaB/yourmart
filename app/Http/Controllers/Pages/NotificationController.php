<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Setting\Notification;
use App\Models\Setting\NotificationSeen;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return view('pages.notification_page');
    }

    public function fectPublicNotification(){

        $data = Notification::where('is_public', '0')->get();

        return (new ResponseCollection( $data ))
            ->response()
            ->setStatusCode(200);
    }

    public function store( Request $request ){

        Notification::create([
            'title'   => $request->title, // Order || Pay-outs || New Arrival
            'message' => $request->message,
            'image'   => $request->image ? $this->attachment($request->image) : "",
            'color'   => $request->color,
        ]);

        return response()->json(['message' => 'Public Notification Posted Successfully!'], 200);
    }


    public function update( Request $request ){

        $notification = Notification::where('id', $request->id)->first();

        $notification->update([
            'title'   => $request->title, // Order || Pay-outs || New Arrival
            'message' => $request->message,
            'image'   => $request->image ? $this->attachment($request->image) : $notification->image,
            'color'   => $request->color,
        ]);

        return response()->json(['message' => 'Public Notification Updated Successfully!'], 200);
    }

    public function delete( Request $request ){

        $notification = Notification::where('id', $request->id)->delete();
        NotificationSeen::where('notification_id', $request->id)->delete();

        return response()->json(['message' => 'Public Notification Deleted Successfully!'], 200);
    }

    public function attachment( $image ){
        $filenameWithExt = $image->getClientOriginalName();
        //get just filename
        $filename        = pathinfo($filenameWithExt);
        //get just extension
        $extension       = $image->extension();
        $nameToStore     = str_replace(' ', '' ,$filename['filename']) . "_" . time() . "." . $extension;
        //Move to folder
        $path    = $image->storeAs('public/uploads/inventory/products/media/', $nameToStore);
        return $nameToStore;
    }
}
