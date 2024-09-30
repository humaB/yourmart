<?php

namespace App\Models\User;

use App\Models\CustomerBank;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

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
        'prodcut_description',
        'comment',
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
        return $this->hasOne(CustomerBank::class,'id','bank_id');
    }

    public function shops(){
        return $this->hasMany(SupplierShop::class,'supplier_id','id');
    }

}
