<?php

namespace App\Models\Inventory\Gate;

use App\Models\Inventory\Product\Variation\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GateInwardDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_purchase_order_gate_pass_details';

    protected $fillable = [
        'igp_id',
        'product_id',
        'quantity',
        'added_by',
    ];

    public function product(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function igp(){
        return $this->belongsTo(GateInward::class, 'igp_id', 'id');
    }

}
