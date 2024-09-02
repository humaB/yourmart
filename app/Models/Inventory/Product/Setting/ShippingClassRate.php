<?php

namespace App\Models\Inventory\Product\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingClassRate extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_shipping_rates';

    protected $fillable = [
        'shipping_class_id',
        'rate_type', // 'free', 'flat', 'weight_based', 'dimension_based'
        'minimum_order',
        'flat_rate',
        'base_rate',
        'rate_per_unit',
        'added_by',
    ];
}
