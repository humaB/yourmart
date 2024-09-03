<?php

namespace App\Models\Inventory\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_tags';

    protected $fillable = [
        'name',
        'slug',
        'added_by'
    ];
}
