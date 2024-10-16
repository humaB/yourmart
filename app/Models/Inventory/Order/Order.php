<?php

namespace App\Models\Inventory\Order;

use App\Models\Account\AccountTransaction;
use App\Models\City;
use App\Models\Inventory\Courier\Courier;
use App\Models\Inventory\Courier\CourierCategoryRange;
use App\Models\Inventory\Store\StoreReturn;
use App\Models\User;
use App\Models\User\DropShipperShop;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // Nomal || Cash || Daraz
        'order_no',
        'customer_name',
        'address',
        'phone_number',
        'phone_number2',
        'city_id',
        'courier_service_id',
        'range_id',
        'courier_service_price',
        'shop_id',
        'instructions',
        'order_note',
        'additional_information',
        'total_bill',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'payment_proof_attachment',
        'selling_price',
        'advance_amount',
        'total_profit',
        'total_paid_profit',
        'packaging_price',
        'is_replacement', // 0 => Normal || 1 => replacement
        'status', // 0 => Order Collection || 1 => Inventory Manager || 2 => QA || 3 => Packing/Dispatch || 4 => Autidor || 5 => Dispatched || 6 => Admin approval for Rejected || 7 => Rejected || 8 => Delivered || 9 => Returned || 10 => Returned to store from leopard || 11 => Out for Delivery
        'total_weight',
        'no_of_labels',
        'tracking_number',
        'slip_link', // from Leopard
        'belongs_to',
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'belongs_to');
    }

    public function courier(){
        return $this->hasOne(Courier::class, 'id', 'courier_service_id');
    }

    public function range(){
        return $this->hasOne(CourierCategoryRange::class, 'id', 'range_id');
    }

    public function activity(){
        return $this->hasMany(OrderActivity::class, 'order_id', 'id');
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

    public function returns(){
        return $this->hasOne(StoreReturn::class);
    }

    public function daraz_labels(){
        return $this->hasMany(OrderLabel::class);
    }

    public function vouchers(){
        return $this->hasMany(AccountTransaction::class, 'posting_id', 'id')
                    ->where('posting_type', 'order')
                    ->whereIn('type', ['BP', 'CP']);
    }
}
