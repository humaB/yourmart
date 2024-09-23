<?php

namespace App\Models\Inventory\Gate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GateInward extends Model
{
    use HasFactory;

    protected $table = 'inventory_purchase_order_gate_passes';

    protected $fillable = [
        'po_id',
        'added_by',
    ];

    public function details(){
        return $this->hasMany(GateInwardDetail::class, 'igp_id', 'id');
    }
}
