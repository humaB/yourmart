<?php

namespace App\Models\Inventory\PurchaseOrder;

use App\Models\User;
use App\Models\User\Supplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'inventory_purchase_orders';

    protected $fillable = [
        'supplier_id',
        'total_amount',
        'remaining_amount',
        'tax',
        'delivery_charges',
        'discount',
        'payment_term_advance',
        'payment_term_after_delivery',
        'approved_by',
        'approved_date',
        'supplier_stock', // 0 => Your Mart || 1 => Supplier Stock
        'status', // 0 => Pending || 1 => Approved || 2 => Rejected
        'added_by',
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function details(){
        return $this->hasMany(PurchaseOrderDetail::class, 'po_id', 'id');
    }

    public function approver(){
        return $this->hasOne(User::class, 'id', 'approved_by');
    }
}
