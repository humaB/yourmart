<?php

namespace App\Models\Inventory\Store;

use App\Models\Inventory\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReturn extends Model
{
    use HasFactory;

    protected $table = 'inventory_store_returns';

    protected $fillable = [
        'order_id',
        'dropshipper_id',
        'remarks',
        'return_type', //0 => Normal || 1 => Returned From Courier
        'added_by'
    ];


    public function details(){
        return $this->hasMany(StoreReturnDetail::class, 'srn_id', 'id');
    }

    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

}
