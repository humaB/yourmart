<?php

namespace App\Models\Account;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountGroup extends Model
{
    use HasFactory, softDeletes;
    protected $fillable = [
        "name",
        "code",
        "account_id", // accounts table id
        "parent_id", // self table id
        "company_id",
        "added_by",
    ];

    public function level_two(){
        return $this->belongsTo( Account::class, 'account_id', 'id');
    }

    public function account_head(){
        return $this->hasMany( AccountHead::class, 'group_id', 'id');
    }

    public function level_three(){
        return $this->belongsTo( AccountGroup::class, 'parent_id', 'id');
    }

    public function dropshipper_shop_ledger(){
        return $this->hasOne( AccountHead::class, 'group_id', 'id');
    }
}
