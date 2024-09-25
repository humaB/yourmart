<?php

namespace App\Models\Inventory\Product\Setting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingClass extends Model
{
    use HasFactory;

    protected $table = 'product_packaging_classes';

    protected $fillable = [
        'name',
        'price',
        'description',
        'is_active',// 1 => Active || 0 => In Active
        'added_by'
    ];

    public function added_name(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}
