<?php

namespace App\Models\Inventory\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQrCode extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_barcodes';

    protected $fillable = [
        'product_variation_id',
        'supplier_id',
        'barcode'
    ];
}
