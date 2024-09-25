<?php

namespace App\Http\Controllers\Inventory\Setting;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResponseCollection;
use App\Models\Inventory\Product\Setting\PackagingClass;
use Illuminate\Support\Facades\DB;
use App\Models\Setting\HomePageSetting;
use App\Models\Setting\LibraryPageSetting;
use App\Models\Setting\HelpCenterPageSetting;
use Illuminate\Http\Request;

class ProductPackagingClassController extends Controller
{
    public function index(){
        return view('inventory.product.setting.packaging_classes');
    }

    public function fectPackagingClassSetting(){
        $data = PackagingClass::with("added_name")->get();

        return (new ResponseCollection( $data ))
            ->response()
            ->setStatusCode(200);
    }

    public function dropDown()
    {
        $record = PackagingClass::orderBy('id', 'desc')->select('id as code', DB::raw("CONCAT(name, ' - ', price) as label"))->get();
        return (new ResponseCollection($record))
            ->response()
            ->setStatusCode(200);
    }

    public function packagingClassSettingStore( Request $request ){

        // Create the record in the page_library_contents table
        PackagingClass::create([
            'name' => $request->name,
            'price' => $request->price,  
            'description' => $request->description,  
            'added_by' => auth()->user()->id, 
        ]);

        return response()->json(['message' => 'Packaging class saved successfully!'], 200);
    }

    public function packagingClassSettingUpdate( Request $request )
    {
        PackagingClass::where("id",$request->id)->update([
            'name' => $request->name,
            'price' => $request->price,  
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'Packaging class saved successfully!'], 200);
    }
}
