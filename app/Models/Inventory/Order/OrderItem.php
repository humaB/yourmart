<?php

namespace App\Models\Inventory\Order;

use App\Models\Inventory\Product\Variation\ProductVariation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_variation_id',
        'price',
        'quantity',
        'sell_price',
        'packaging_cost',
        'courier_cost',
        'belongs_to'
    ];

    public function variation(){
        return $this->belongsTo(ProductVariation::class, 'product_variation_id', 'id');
    }

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
