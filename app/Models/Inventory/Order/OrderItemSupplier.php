<?php

namespace App\Models\Inventory\Order;

use App\Models\Inventory\Product\Variation\Product;
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


    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function order_items(){
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}
