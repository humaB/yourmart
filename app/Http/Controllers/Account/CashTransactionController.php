<?php

namespace App\Http\Controllers\Account;

use App\Models\Account\Account;
use App\Models\Account\Bank;
use App\Models\Account\Cash;
use App\Models\Account\AccountGroup;
use App\Models\Account\AccountTransaction;
use App\Models\Account\BankTransaction;
use App\Models\Account\AccountReceipt;
use App\Models\Account\CashTransaction;
use App\Models\Account\AccountHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Routing\Controller as BaseController;

class CashTransactionController extends BaseController
{

    public function cashTransactionIndex(Request $request)
    {
        return view('account.cash_transaction');
    }

    public function cashTransactions(Request $request)
    {
        // dropdown data
        $cashes = Cash::
        join('account_heads', 'cash.account_head_id', 'account_heads.id')
            ->select('account_heads.*','account_heads.id as code', 'account_heads.name as label')
            ->get();
        $heads = AccountHead::with('level_four:id,name')
        ->get()
        ->map(function($single) {
            return [
                'code' => $single->id,
                'label' => $single->name . ' (' . $single->level_four->name . ')',
                'name' => $single->name,
            ];
        });
       
        // main page data
        $cashTransactions = AccountTransaction::
        where(function($q){
            $q->where("type","CP");
            $q->orWhere("type","CR");
        })
        ->orderBy("id",'ASC')
        ->get()
        ->groupBy('document_id')
        ->map(function ($group) {
            $column = $group->first()->type=="CP" ? "debit" : "credit";
            return [
                'id' => $group->first()->id,
                'document_id' => $group->first()->document_id ?? null,
                'cash_name' => $group->first()->account_head->name ?? 'N/A',
                'amount' => $group->sum($column),
                'added_by' => $group->first()->added_by_name->name ?? 'N/A',
                'type' => $group->first()->type,
                'approved' => $group->first()->approved,
                'approved_by' => $group->first()->approved_by_name->name ?? 'N/A',
                'posted_date' => date("H:i d/m/Y",strtotime($group->first()->created_at)),
            ];
        })->values();

        return [
            "cashes" => $cashes,
            "heads" => $heads,
            "cashTransactions" => $cashTransactions,
        ];
    }

    public function cashTransactionAdd(Request $request)
    {
        $request->validate([
            'type' => ['required'],
            'cash_ledger' => ['required'],
            'narration' => ['required'],
            'ledgers' => ['required'],
            'amounts' => ['required'],
            'narrations' => ['required'],
        ]);

        try {
            DB::beginTransaction();
            
            // to create document serial of BP/BR
            $document = AccountTransaction::
            where(function($q){
                $q->where("type","CP");
                $q->orWhere("type","CR");
            })
            ->orderBy("document_id","DESC")
            ->first();
            $document_id = $document ? $document->document_id + 1 : 1;
    
            // receipt id only generate when voucher will approved
            $posting = AccountTransaction::create([
                'account_head_id' => $request->cash_ledger,
                'other_account_head_id' => $request->ledgers[0],
                'credit' => $request->type=="CP" ? array_sum(array_map('floatval', $request->amounts)) : 0,
                'debit' => $request->type=="CP" ? 0 : array_sum(array_map('floatval', $request->amounts)),
                'document_id' => $document_id,
                'type' => $request->type,
                'narration' => strtoupper($request->narration),
                'posting_type' => '',
                'posting_id' => null,
                'added_by'           => Auth::user()->id,
            ]);
    
            foreach ($request->ledgers as $i => $value) {
                AccountTransaction::create([
                    'account_head_id' => $request->ledgers[$i],
                    'other_account_head_id' => $request->cash_ledger,
                    'credit' => $request->type=="CP" ? 0 : $request->amounts[$i],
                    'debit' => $request->type=="CP" ? $request->amounts[$i] : 0,
                    'document_id' => $document_id,
                    'type' => $request->type,
                    'narration' => strtoupper($request->narrations[$i]),
                    'posting_type' => '',
                    'posting_id' => null,
                    'added_by' => Auth::user()->id,
                ]);
            }

           

            DB::commit();
            return response()->json([],201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Database transaction error: ' . $e->getMessage());
            return response()->json(["errors" => ["Error" => ['An error occurred during the database transaction.']]], 422);
        }

    }

    public function cashTransaction(Request $request)
    {
        $column = $request->type=="CP" ? "debit" : "credit";
        $transaction = AccountTransaction::
        where(['type'=>$request->type,"document_id"=>$request->id])
        ->orderBy('id','ASC')
        ->first();

        // to get posting ids
        $transaction->postings = AccountTransaction::orderBy('id','ASC')
        ->where(['type'=>$request->type,"document_id"=>$request->id])
        ->where("id",'!=', $transaction->id)
        ->where("other_account_head_id", $transaction->account_head_id)->pluck("id");
        
        // to get receiver ledgers
        $transaction->ledgers = AccountTransaction::orderBy('id','ASC')
        ->where(['type'=>$request->type,"document_id"=>$request->id])
        ->where("id",'!=', $transaction->id)
        ->where("other_account_head_id", $transaction->account_head_id)->pluck("account_head_id");
        
        // to get receiver amounts
        $transaction->amounts = AccountTransaction::orderBy('id','ASC')
        ->where(['type'=>$request->type,"document_id"=>$request->id])
        ->where("id",'!=', $transaction->id)
        ->where("other_account_head_id", $transaction->account_head_id)->pluck($column);
        
        // to get receiver narration
        $transaction->narrations = AccountTransaction::orderBy('id','ASC')
        ->where(['type'=>$request->type,"document_id"=>$request->id])
        ->where("id",'!=', $transaction->id)
        ->where("other_account_head_id", $transaction->account_head_id)->pluck("narration");

        return [
            "transaction" => $transaction,
        ];
    }
    
    public function cashTransactionDetail(Request $request)
    {
        $transaction = AccountTransaction::
        where(['type'=>$request->type,"document_id"=>$request->id])
        ->orderBy("id",'ASC')
        ->with("account_head.level_four:id,code","account_head.level_three:id,code","account_head.level_two:id,code","account_head.level_one:id,code")
        ->with("added_by_name:id,name","updated_by_name:id,name")
        ->get();

        return [
            "transaction" => $transaction,
        ];
    }
    
    public function approveCashTransaction(Request $request)
    {
        try {
            DB::beginTransaction();

            $trans = AccountTransaction::
            where(["document_id"=>$request->id,'type'=>$request->type])->get();
            // to create receipt serial of BP/BR/CP/CR
            $receipt_id = null;
            if($trans->count() == 2)
            {
                $receipt = AccountTransaction::
                where(function($q){
                    $q->where("type","BP");
                    $q->orWhere("type","BR");
                    $q->orWhere("type","CP");
                    $q->orWhere("type","CR");
                })
                ->orderBy("receipt_id","DESC")
                ->first();
                $receipt_id = $receipt ? $receipt->receipt_id + 1 : 1;
            }

            foreach($trans as $single)
            {

                $head = AccountHead::where(["id"=>$single->account_head_id])
                ->first();

                AccountTransaction::
                where(["id"=>$single->id])
                ->update([
                    "approved" => 1,
                    "approved_by" => Auth::user()->id,
                    "receipt_id" => $receipt_id,
                    "parent_account_id" => $head->parent_account_id,
                    "account_id" => $head->account_id,
                    "parent_group_id" => $head->parent_group_id,
                    "group_id" => $head->group_id
                ]);
            }
            
            
            DB::commit();

            return response()->json([],200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Database transaction error: ' . $e->getMessage());
            return response()->json(["message" => 'An error occurred during the database transaction'], 409);
        }
    }

    public function cashTransactionUpdate(Request $request)
    {
        $request->validate([
            'type' => ['required'],
            'account_head_id' => ['required'],
            'narration' => ['required'],
            'ledgers' => ['required'],
            'amounts' => ['required'],
            'narrations' => ['required'],
        ]);

        try {
            DB::beginTransaction();
    
            $oldTrans = AccountTransaction::where("id",$request->id)->first();

            AccountTransaction::where(["type"=>$oldTrans->type,"document_id"=>$oldTrans->document_id])
            ->delete();
            
            $posting = AccountTransaction::create([
                'account_head_id' => $request->account_head_id,
                'other_account_head_id' => $request->ledgers[0],
                'credit' => $request->type=="CP" ? array_sum(array_map('floatval', $request->amounts)) : 0,
                'debit' => $request->type=="CP" ? 0 : array_sum(array_map('floatval', $request->amounts)),
                'narration' => strtoupper($request->narration),
                'document_id' => $oldTrans->document_id,
                'type' => $request->type,
                'posting_type' => '',
                'posting_id' => null,
                'added_by' => $oldTrans->added_by ?? Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'created_at' => $oldTrans->created_at ?? now(),
            ]);
    
            foreach ($request->ledgers as $i => $value) {
                    AccountTransaction::create([
                        'account_head_id' => $request->ledgers[$i],
                        'other_account_head_id' =>$request->account_head_id,
                        'credit' => $request->type=="CP" ? 0 : $request->amounts[$i],
                        'debit' => $request->type=="CP" ? $request->amounts[$i] : 0,
                        'document_id' => $oldTrans->document_id,
                        'type' => $oldTrans->type,
                        'narration' => strtoupper($request->narrations[$i]),
                        'posting_type' => '',
                        'posting_id' => null,
                        'added_by' => $oldTrans->added_by ?? Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                        'created_at' => $oldTrans->created_at ?? now(),
                    ]);
            }


            DB::commit();
            return response()->json([],200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Database transaction error: ' . $e->getMessage());
            return response()->json(["errors" => ["Error" => ['An error occurred during the database transaction.']]], 422);
        }

    }
}
