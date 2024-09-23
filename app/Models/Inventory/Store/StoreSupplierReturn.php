<?php

namespace App\Models\Inventory\Store;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSupplierReturn extends Model
{
    use HasFactory;

    protected $table = 'inventory_supplier_returns';

    protected $fillable = [
        'po_id',
        'supplier_id',
        'remarks',
        'added_by'
    ];
    
}
