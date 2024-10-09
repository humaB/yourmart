<?php

namespace App\Http\Controllers\Account\Helper;

use App\Models\Account\AccountGroup;
use App\Models\Account\AccountHead;
use App\Models\Account\AccountTransaction;

class AccountHeadHelper {

    public function accountGroupFourthCreate($name, $second, $third) {

        $code = AccountGroup::latest('id')->where('parent_id', $third )->limit(1)->value('code') + 1;
       $code = str_pad($code, 3, '0', STR_PAD_LEFT);

        $group = AccountGroup::create([
            'name'       => strtoupper($name),
            'code'       =>  $code,
            'account_id' =>  $second,
            'parent_id'  =>  $third,
            'added_by'   => auth()->user()->id ?? 0
        ]);

        return $group;
    }

    public function accountHeadCreate($name, $first, $second, $third, $fourth) {

        $code = AccountHead::latest('id')->where('group_id', $fourth )->limit(1)->value('code') + 1;
        $code = str_pad($code, 4, '0', STR_PAD_LEFT);

        $head = AccountHead::create([
            'name'              => strtoupper($name),
            'code'              => $code,
            'parent_account_id' => $first,
            'account_id'        => $second,
            'parent_group_id'   => $third,
            'group_id'          => $fourth,
            'added_by'          => auth()->user()->id ?? 0
        ]);

        return $head;
    }

    public function voucherType( $type ){
              // to create document serial of BP/BR
              $document = AccountTransaction::where(function($q) use ($type){
                  if($type == 'cash'){
                      $q->where("type","CP")->orWhere("type","CR");
                  }
                  else if($type == 'bank'){
                      $q->where("type","BP")->orWhere("type","BR");
                  }
                  else{
                      $q->where("type","JV");
                  }
              })
              ->orderBy("document_id","DESC")
              ->first();

              $document_id = $document ? $document->document_id + 1 : 1;
              return $document_id;
    }

    public function accountTransaction($head, $otherHead, $debit, $credit, $narration, $document_id, $type, $postingType, $postingID, $approved = 0){
            // receipt id only generate when voucher will approved
            $posting = AccountTransaction::create([
                'account_head_id' => $head,
                'other_account_head_id' => $otherHead,
                'debit'          => $debit,
                'credit'         => $credit,
                'narration'      => $narration,
                'document_id'    => $document_id,
                'type'           => $type,
                'posting_type'   => $postingType,
                'posting_id'     => $postingID,
                'cheque'         => null,
                'approved'       => $approved,
                'added_by'       => auth()->user()->id ?? 0,
            ]);
    }
}
