<?php

namespace App\Models\Inventory\Product\Variation;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscountPerQty extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_discount_per_qty';

    protected $fillable = [
        'product_id',
        'quantity',
        'price', // Discounted price
        'added_by',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}
