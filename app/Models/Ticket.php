<?php

namespace App\Models;

use App\Models\Inventory\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'order_no',
        'ticket_type',
        'message',
        'expected_result',
        'file_path',
        'status', // Closed || In-Process ||
        'added_by',
        'updated_by',
    ];

    public function added_by_name(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_no');
    }

}
