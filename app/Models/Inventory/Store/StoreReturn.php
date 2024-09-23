<?php

namespace App\Models\Inventory\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReturn extends Model
{
    use HasFactory;

    protected $table = 'inventory_store_returns';

    protected $fillable = [
        'order_id',
        'dropshipper_id',
        'remarks',
        'added_by'
    ];

}
