<?php

namespace App\Models\Inventory\Product\Setting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinimumOrderQuantity extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_minimum_order_quantities';

    protected $fillable = [
        'status',// 0 => Active || 1 => In Active
        'type', // 0 => Guest || 1 => Registered
        'quantity',
        'added_by'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}
