<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DropShipperLevel extends Model
{
    use HasFactory;

    protected $table = 'drop_shipper_levels';

    protected $fillable = [
        'dropshipper_id',
        'user_id',
        'level',// New Seller || Level 01 || Level 02 || Level 03 || Top Rated Seller
        'is_completed',// 0 => Not Complete || 1 => Completed
        'is_active' // 0 => Not Active || 1 => Active
    ];

    public function details(){
        return $this->hasOne(DropShipperLevelDetail::class,'dropshipper_level_id','id');
    }

    public function dropshipper(){
        return $this->belongsTo(DropShipper::class,'dropshipper_id','id');
    }
}
