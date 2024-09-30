<?php

namespace App\Http\Controllers\Account;

use App\Models\Account\Account;
use App\Models\Account\AccountGroup;
use App\Models\Account\AccountHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Routing\Controller as BaseController;

class AccountController extends BaseController
{
    public function groupIndex(Request $request)
    {
        return view('account.group');
    }

    public function accountGroups(Request $request)
    {
        $secondLevel = Account::where('parent_id' , '!=' ,'0')->get(['id as code', 'name as label']);
       
        $fourthLevel = AccountGroup::where('parent_id','!=','0')
        ->with('level_two:id,name,code', 'level_three:id,name,code')
        ->orderBy('name')
        ->get();

        return [
            "secondLevel" => $secondLevel,
            "fourthLevel" => $fourthLevel,
        ];
    }

    public function groupStore(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('account_groups','name'),
            ],
            'second_level' => [
                'required',
            ],
        ]);

        if($request->group_type == "tier 4")
        {
            $request->validate([
                'third_level' => 'required',
            ]);
        }
        try {
            DB::beginTransaction();
            
    
            if( $request->group_type == 'tier 3' ){
                $code = AccountGroup::latest('id')->where('account_id', $request->second_level )->where('parent_id', 0 )->limit(1)->value('code') + 1;
                $code = str_pad($code, 2, '0', STR_PAD_LEFT);
            }else{
                $code = AccountGroup::latest('id')->where('parent_id', $request->third_level )->limit(1)->value('code') + 1;
                $code = str_pad($code, 3, '0', STR_PAD_LEFT);
            }

            $group = AccountGroup::create([
                'name'       => strtoupper($request->name),
                'code'       => $code,
                'account_id' => $request->second_level, 
                'parent_id'  => $request->third_level,
                'company_id'  => 0,
                'added_by'         => Auth::user()->id
            ]);

            DB::commit();
            return response()->json([],201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Database transaction error: ' . $e->getMessage());
            return response()->json(["errors" => ["Error" => ['An error occurred during the database transaction.']]], 422);
        }

    }
    
    public function groupUpdate(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                Rule::unique('account_groups','name')->ignore($request->id),
            ],
        ]);

        try {
            DB::beginTransaction();
            

            AccountGroup::where("id",$request->id)->update([
                'name'       => strtoupper($request->name),
            ]);

            DB::commit();
            return response()->json([],200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Database transaction error: ' . $e->getMessage());
            return response()->json(["errors" => ["Error" => ['An error occurred during the database transaction.']]], 422);
        }

    }

    public function secondLevelOfFirst($first)
    {
        $secondLevel = Account::where(["parent_id" => $first])->orderBy('id')->get(["id","name as text"]);
        
        return [
            "secondLevel" => $secondLevel,
        ];
    }
    
    public function thirdLevelOfSecond($second)
    {
        $thirdLevel = AccountGroup::where(["account_id" => $second])->where('parent_id','0')->orderBy('id')->get(['id as code', 'name as label']);
        
        return [
            "thirdLevel" => $thirdLevel,
        ];
    }
    
    public function fourthLevelOfThird($third)
    {
        $fourthLevel = AccountGroup::where(["parent_id" => $third])->orderBy('id')->get(["id","name as text"]);
        
        return [
            "fourthLevel" => $fourthLevel,
        ];
    }

    function accountHeadCreate($name, $first, $second, $third, $fourth) {

        $code = AccountHead::latest('id')->where('group_id', $fourth )->limit(1)->value('code') + 1;
        $code = str_pad($code, 4, '0', STR_PAD_LEFT);

        $head = AccountHead::create([
            'name' => strtoupper($name),
            'code' => $code,
            'parent_account_id' => $first,
            'account_id' => $second,
            'parent_group_id' => $third,
            'group_id' => $fourth,
            'company_id' => Auth::user()->company_id,
            'added_by' => Auth::user()->id
        ]);

        return $head;
    }
}
