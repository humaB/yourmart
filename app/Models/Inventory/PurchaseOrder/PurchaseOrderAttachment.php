<?php

namespace App\Models\Inventory\PurchaseOrder;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderAttachment extends Model
{
    use HasFactory;

    protected $table = 'inventory_purchase_order_attachments';

    protected $fillable = [
        'po_id',
        'file',
        'attachment'
    ];
}
