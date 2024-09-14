<?php

namespace App\Models\User;

use App\Models\Bank;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropShipper extends Model
{
    use HasFactory;

    protected $table = 'drop_shippers';

    protected $fillable = [
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
        // Optional Fields
        'store_name',
        'store_url',
        'social_media_profile_link',
        'business_description',
        // Image Fields
        'cnic_front_image',
        'cnic_back_image',
        'profile_image',
        'status' // 0 => Pending | 1 => Approved | 2 => Rejected
    ];

    public function city(){
        return $this->hasOne(City::class ,'id','city_id');
    }

    public function bank(){
        return $this->hasOne(Bank::class,'id','bank_id');
    }

}
