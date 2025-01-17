<?php

namespace App\Models\Inventory\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDispatchedRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tracking_number',
        'total_amount',
        'added_by'
    ];

    public function order(){
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

}
