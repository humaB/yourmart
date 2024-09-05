<?php

namespace App\Models\Inventory\Product\Variation;

use App\Models\Inventory\Product\AttributeType;
use App\Models\User;
use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_attributes';

    protected $fillable = [
        'product_id',
        'attribute_id',
        'added_by',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function attribute(){
        return $this->belongsTo(AttributeType::class, 'attribute_id', 'id');
    }

}
