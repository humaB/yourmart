<?php

namespace App\Models\Inventory\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLabel extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'attachment'
    ];
}
