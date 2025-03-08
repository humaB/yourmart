<?php

namespace App\Models\Inventory\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReAttempt extends Model
{
    use HasFactory;

    protected $table = 'order_reattempts';

    protected $fillable = [
        'order_id',
        'advice'
    ];
}
