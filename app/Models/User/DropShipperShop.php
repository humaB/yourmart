<?php

namespace App\Models\User;

use App\Models\Inventory\Order\Order;
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

        'total_payable',
        'total_paid',
        'total_remaining',

        'leopard_id',
    ];

    public function dropshipper(){
        return $this->belongsTo(DropShipper::class, 'dropshipper_id', 'id');
    }

    public function delivered_orders(){
        return $this->hasMany(Order::class, 'shop_id', 'id')->where('status', '8');
    }

    public function returned_orders(){
        return $this->hasMany(Order::class, 'shop_id', 'id')->whereIn('status', ['9', '10']);
    }
}
