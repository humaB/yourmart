<?php

namespace App\Models\Inventory\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOboseleteProductDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_oboselete_product_details';

    protected $fillable = [
        'pob_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'added_by',
    ];

}
