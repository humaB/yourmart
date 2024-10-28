<?php

namespace App\Models\Inventory\Store;

use App\Models\Inventory\Product\Variation\Product;
use App\Models\Inventory\Product\Variation\ProductVariation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReturnDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_store_return_details';

    protected $fillable = [
        'srn_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'added_by',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function srn(){
        return $this->belongsTo(StoreReturn::class, 'srn_id', 'id');
    }

}
