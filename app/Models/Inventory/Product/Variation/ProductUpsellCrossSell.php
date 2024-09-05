<?php

namespace App\Models\Inventory\Product\Variation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUpsellCrossSell extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_publish_upsell_and_crosssells';

    protected $fillable = [
        'product_id',
        'type',
        'reference_product_id',
        'added_by',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'reference_product_id', 'id');
    }

}
