<?php

namespace App\Models\Inventory\Order;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'activity',
        'added_by'
    ];


    public function user(){
        return $this->hasOne(User::class, 'id', 'added_by');
    }

}
