<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropShipperLevelDetail extends Model
{
    use HasFactory;

    protected $table = 'drop_shipper_level_details';

    protected $fillable = [
        'dropshipper_level_id',
        'requirement',
        'is_completed',// 0 => Not Complete || 1 => Completed
    ];

    protected $casts = [
        'requirement' => 'array', // Laravel will cast JSON to array automatically
    ];
}
