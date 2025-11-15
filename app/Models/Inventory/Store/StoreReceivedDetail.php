<?php

namespace App\Models\Inventory\Store;

use App\Models\Inventory\Product\Variation\Product;
use App\Models\User\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReceivedDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_purchase_order_store_received_details';

    protected $fillable = [
        'grn_id',
        'supplier_id',
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

    public function supplier(){
        return $this->hasOne(Supplier::class, 'id', 'supplier_id');
    }

    public function grn(){
        return $this->belongsTo(StoreReceived::class, 'grn_id', 'id');
    }

}
