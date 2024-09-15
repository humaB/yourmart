<?php

namespace App\Models\Inventory\Order;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'comment',
        'attachment',
        'added_by'
    ];

    public function user(){
        return $this->belongsTo( User::class, 'added_by', 'id');
    }
}
