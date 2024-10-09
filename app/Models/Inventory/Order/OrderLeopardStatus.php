<?php

namespace App\Models\Inventory\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLeopardStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'leopard_label',
        'short_code',
        'internal_label',
        'receiver_name',
        'reason',
        'time'
    ];
}
