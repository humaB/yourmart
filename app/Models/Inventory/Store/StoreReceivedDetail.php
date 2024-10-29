<?php

namespace App\Models\Inventory\Store;

use App\Models\Inventory\Product\Variation\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReceivedDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_purchase_order_store_received_details';

    protected $fillable = [
        'grn_id',
        'product_id',
        'quantity',
        'price',
        'tax',
        'delivery_charges',
        'discount',
        'total',
        'remarks',
        'added_by',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function grn(){
        return $this->belongsTo(StoreReceived::class, 'grn_id', 'id');
    }

}
