<?php

namespace App\Models\Inventory\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreIssuanceDetail extends Model
{
    use HasFactory;

    protected $table = 'inventory_issuance_details';

    protected $fillable = [
        'sin_id',
        'product_id',
        'quantity',
        'price',
        'total',
        'added_by',
    ];
}
