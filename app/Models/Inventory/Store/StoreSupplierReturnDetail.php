<?php

namespace App\Models\Inventory\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSupplierReturnDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_supplier_return_details';

    protected $fillable = [
        'vrn_id',
        'product_id',
        'quantity',
        'price',
        'tax',
        'discount',
        'total',
        'added_by'
    ];

}
