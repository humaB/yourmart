<?php

namespace App\Models\Inventory\Product\Setting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherCharge extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_other_charges';

    protected $fillable = [
        'type', // Order Return || Daraz Packaging
        'amount',
        'status',// 0 => Active || 1 => In Active
        'added_by'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}
