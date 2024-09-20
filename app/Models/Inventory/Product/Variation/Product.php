<?php

namespace App\Models\Inventory\Product\Variation;

use App\Models\Inventory\Product\Brand;
use App\Models\Inventory\Product\Category;
use App\Models\Inventory\Product\Setting\ShippingClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inventory_products';

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'brand_id',
        'category_id',
        'shipping_method_id',
        'hero_image',
        'video_link',
        'product_description',
        'product_highlight',
        'product_highlights',
        'warranty',
        'max_quantity',
        'quantity_step',
        'status', // 0 => Published || 1 => Draft || 2 => Schedule
        'added_by',
        'deleted_at'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function category(){
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    public function brand(){
        return $this->hasOne(Brand::class, 'id', 'brand_id');
    }

    public function shipping(){
        return $this->hasOne(ShippingClass::class, 'id', 'shipping_method_id');
    }

    public function attributes(){
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id');
    }

    public function tags(){
        return $this->hasMany(ProductTag::class, 'product_id', 'id');
    }

    public function dimensions(){
        return $this->hasOne(ProductDimension::class, 'product_id', 'id');
    }

    public function discounts(){
        return $this->hasMany(ProductDiscountPerQty::class, 'product_id', 'id');
    }

    public function saleSchedule(){
        return $this->hasOne(ProductSaleSchedule::class, 'product_id', 'id');
    }

    public function variations(){
        return $this->hasMany(ProductVariation::class, 'product_id', 'id');
    }

    public function variation(){
        return $this->hasOne(ProductVariation::class, 'product_id', 'id');
    }

    public function up_sells(){
        return $this->hasMany(ProductUpsellCrossSell::class, 'product_id', 'id')->where('type', 'upsell');
    }

    public function cross_sells(){
        return $this->hasMany(ProductUpsellCrossSell::class, 'product_id', 'id')->where('type', 'cross sell');
    }

    public function bought_togethers(){
        return $this->hasMany(ProductUpsellCrossSell::class, 'product_id', 'id')->where('type', 'bought togethers');
    }

    public function images(){
        return $this->hasMany(ProductVariationImage::class, 'product_id', 'id');
    }


}
