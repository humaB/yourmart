<?php

namespace App\Models\User;

use App\Models\Account\AccountGroup;
use App\Models\Account\AccountTransaction;
use App\Models\CustomerBank;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropShipper extends Model
{
    use HasFactory;

    protected $table = 'drop_shippers';

    protected $fillable = [
        'leopard_id', //Defualt Zero Not to be used for future reference
        'group_id',
        'full_name',
        'email',
        'password',
        'cnic_number',
        'whatsapp_number',
        'address',
        'city_id',
        'bank_id',
        'account_number',
        'account_title',
        'account_iban',
        'payment_cycle',

        'total_payable',
        'total_paid',
        'remaining_amount',
        // Optional Fields
        'store_name',
        'store_url',
        'social_media_profile_link',
        'business_description',
        // Image Fields
        'cnic_front_image',
        'cnic_back_image',
        'profile_image',
        'status', // 0 => Pending | 1 => Approved | 2 => Rejected | 3 => Deactivate
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class ,'user_id','id');
    }

    public function city(){
        return $this->hasOne(City::class ,'id','city_id');
    }

    public function bank(){
        return $this->hasOne(CustomerBank::class,'id','bank_id');
    }

    public function shop(){
        return $this->hasOne(DropShipperShop::class,'dropshipper_id','id');
    }

    public function shops(){
        return $this->hasMany(DropShipperShop::class,'dropshipper_id','id');
    }

    public function general_ledger(){
        return $this->hasOne(AccountGroup::class, 'id', 'group_id');
    }

}
