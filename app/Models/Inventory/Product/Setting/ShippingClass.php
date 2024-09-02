<?php

namespace App\Models\Inventory\Product\Setting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingClass extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_shipping_classes';

    protected $fillable = [
        'name',
        'description',
        'is_active',// 0 => Active || 1 => In Active
        'added_by'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function details(){
        return $this->hasOne(ShippingClassRate::class, 'shipping_class_id', 'id');
    }
}
