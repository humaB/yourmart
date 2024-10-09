<?php

namespace App\Models\Account;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, softDeletes;
    protected $fillable = [
        "name",
        "address",
        "iban",
        "account_number",
        "balance",
        "status",
        "account_head_id",
        "company_id",
        "added_by",
        "updated_by",
    ];

    // public function level_one(){
    //     return $this->belongsTo( Account::class, 'parent_account_id', 'id');
    // }
}
