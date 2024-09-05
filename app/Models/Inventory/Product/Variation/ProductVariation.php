<?php

namespace App\Models\Inventory\Product\Variation;

use App\Models\Inventory\Product\Color;
use App\Models\Inventory\Product\Size;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_variations';

    protected $fillable = [
        'product_id',
        'sku',
        'color_id',
        'size_id',
        'regular_price',
        'sale_price',
        'stock',
        'added_by',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function color(){
        return $this->hasOne(Color::class, 'id', 'color_id');
    }

    public function size(){
        return $this->hasOne(Size::class, 'id', 'size_id');
    }

    public function images(){
        return $this->hasMany(ProductVariationImage::class, 'product_variation_id', 'id');
    }

}
