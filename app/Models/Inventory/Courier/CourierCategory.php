<?php

namespace App\Models\Inventory\Courier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'internal_label',
        'description',
        'added_by'
    ];

}
