<?php

namespace App\Models\Inventory\Store;

use App\Models\Inventory\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreIssuance extends Model
{
    use HasFactory;

    protected $table = 'inventory_issuances';

    protected $fillable = [
        'order_id',
        'added_by',
    ];

    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

}
