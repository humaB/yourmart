<?php

namespace App\Models\Inventory\Store;

use App\Models\Inventory\Product\Variation\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreReceived extends Model
{
    use HasFactory;

    protected $table = 'inventory_purchase_order_store_received';

    protected $fillable = [
        'po_id',
        'added_by',
    ];

    public function details(){
        return $this->hasMany(StoreReceivedDetail::class, 'grn_id', 'id');
    }

}
