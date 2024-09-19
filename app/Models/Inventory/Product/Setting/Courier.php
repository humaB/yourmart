<?php

namespace App\Models\Inventory\Product\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $table = 'couriers';

    protected $fillable = [
        'name',       // Courier name
        'address',    // Courier address (nullable)
        'contact',    // Courier contact (nullable)
        'created_by', // User who added the courier
    ];
}