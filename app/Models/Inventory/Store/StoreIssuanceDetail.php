<?php

namespace App\Models\Inventory\Store;

use App\Models\Inventory\Product\Variation\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreIssuanceDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_issuance_details';

    protected $fillable = [
        'sin_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'remarks',
        'added_by',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function sin(){
        return $this->belongsTo(StoreIssuance::class, 'sin_id', 'id');
    }
}
