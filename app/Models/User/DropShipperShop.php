<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropShipperShop extends Model
{
    use HasFactory;

    protected $table = 'drop_shipper_shops';

    protected $fillable = [
        'dropshipper_id',
        'store_name',
        'store_url',
        'social_media_profile_link',
        'business_description',
    ];
}
