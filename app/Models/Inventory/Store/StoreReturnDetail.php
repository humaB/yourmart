<?php

namespace App\Models\Inventory\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReturnDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_store_return_details';

    protected $fillable = [
        'srn_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'added_by',
    ];

}
