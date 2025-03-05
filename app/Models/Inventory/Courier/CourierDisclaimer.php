<?php

namespace App\Models\Inventory\Courier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierDisclaimer extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_id',
        'disclaimer'
    ];
}
