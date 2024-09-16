<?php

namespace App\Models\Inventory\Order;

use App\Models\City;
use App\Models\User;
use App\Models\User\DropShipperShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'address',
        'phone_number',
        'phone_number2',
        'city_id',
        'courier_service_id',
        'shop_id',
        'instructions',
        'order_note',
        'total_bill',
        'paid_amount',
        'selling_price',
        'status', // 0 => Order Collection || 1 => Inventory Manager || 2 => QA || 3 => Packing/Dispatch || 4 => Delivered
        'belongs_to'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'belongs_to');
    }

    public function city(){
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function shop(){
        return $this->hasOne(DropShipperShop::class, 'id', 'shop_id');
    }

    public function items(){
        return $this->hasMany(OrderItem::class);
    }

    public function comments(){
        return $this->hasMany(OrderComment::class);
    }
}
