<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User\DropShipper;

class DropShipperShop extends Model
{
    use HasFactory;

    protected $table = 'drop_shipper_shops';

    protected $fillable = [
        'dropshipper_id',
        'account_head_id',
        'store_name',
        'store_url',
        'social_media_profile_link',
        'business_description',
        'leopard_id',
    ];

    public function dropshipper(){
        return $this->belongsTo(DropShipper::class, 'dropshipper_id', 'id');
    }
}
