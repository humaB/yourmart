<?php

namespace App\Models\Inventory\Courier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierAddedCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_id',
        'category_id',
        'added_by'
    ];
}
