<?php

namespace App\Models\Inventory\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreIssuance extends Model
{
    use HasFactory;

    protected $table = 'inventory_issuances';

    protected $fillable = [
        'order_id',
        'added_by',
    ];

}
