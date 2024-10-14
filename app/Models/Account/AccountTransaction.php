<?php

namespace App\Models\Account;

use App\Models\Inventory\Order\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountTransaction extends Model
{
    use HasFactory, softDeletes;
    protected $fillable = [
        "account_head_id",
        "other_account_head_id",
        "debit",
        "credit",
        "document_id",
        "type",
        "narration",
        "receipt_id",
        "posting_type",
        "posting_id",
        "cheque",
        "approved", // 1 for approved, 2 for pending
        "approved_by",
        "parent_account_id", // first tier id
        "account_id", // second tier id
        "parent_group_id", // third tier id
        "group_id", // fourt tier id
        "time",
        "company_id",
        "added_by",
        "updated_by",
    ];

    public function added_by_name()
    {
        return $this->belongsTo( User::class, 'added_by', 'id');
    }

    public function other_head_name()
    {
        return $this->belongsTo( AccountHead::class , 'other_account_head_id', 'id');
    }

    public function updated_by_name()
    {
        return $this->belongsTo( User::class, 'updated_by', 'id');
    }

    public function approved_by_name()
    {
        return $this->belongsTo( User::class, 'approved_by', 'id');
    }

    public function account_head()
    {
        return $this->belongsTo( AccountHead::class, 'account_head_id', 'id');
    }

    public function account_receivers()
    {
        return $this->hasMany( AccountTransaction::class, 'other_account_head_id', 'account_head_id');
    }

    public function level_two()
    {
        return $this->belongsTo( Account::class, 'account_id', 'id');
    }

    public function level_four()
    {
        return $this->belongsTo( AccountGroup::class, 'group_id', 'id');
    }

    public function order(){
        return $this->belongsTo( Order::class, 'posting_id', 'id');
    }
}
