<?php

namespace App\Models\Inventory\PurchaseOrder;

use App\Models\Inventory\Product\Variation\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_purchase_order_details';

    protected $fillable = [
        'po_id',
        'product_id',
        'product_variation_id',
        'gate_received_quantity',
        'store_received_quantity',
        'quantity',
        'price',
        'tax',
        'delivery_charges',
        'discount',
        'total',
        'added_by',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

}
