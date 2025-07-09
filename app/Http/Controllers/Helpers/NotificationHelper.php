<?php

namespace App\Http\Controllers\Helpers;

use App\Models\Setting\Notification;

class NotificationHelper
{
    public static function addNotification($title, $messge, $link = null, $image = null, $directImage = null, $color, $isPublic = 0, $user = null){

         $color = match($color) {
            'orange' => 'Message Message--orange',
            'green'  => 'Message Message--green',
            'red'    => 'Message Message--red',
            default  => 'Message',
        };

        Notification::create([
            'title'    => $title, // Order || Pay-outs || New Arrival
            'message'  =>  $messge,
            'link'     => $link,
            'image'   => $directImage ? $directImage : ($image ? self::attachment($image) : ""),
            'color'   => $color,
            'is_public' => $isPublic, // 0 => Public || 1 => Specific User
            'user_id'   => $user,
        ]);
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
