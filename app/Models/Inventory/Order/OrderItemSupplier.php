<?php

namespace App\Models\Inventory\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'supplier_id',
        'product_id',
        'quantity'
    ];
}
