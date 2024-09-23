<?php

namespace App\Models\Inventory\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreOboseleteProduct extends Model
{
    use HasFactory;

    protected $table = 'inventory_oboselete_products';

    protected $fillable = [
        'type',
        'remarks',
        'added_by',
    ];
}
