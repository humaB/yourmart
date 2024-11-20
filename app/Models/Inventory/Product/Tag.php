<?php

namespace App\Models\Inventory\Product;

use App\Models\Inventory\Product\Variation\ProductTag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_tags';

    protected $fillable = [
        'name',
        'slug',
        'added_by'
    ];

    public function tagged(){
        return $this->hasMany(ProductTag::class, 'tag_id');
    }

}
