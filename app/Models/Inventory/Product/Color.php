<?php

namespace App\Models\Inventory\Product;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_colors';

    protected $fillable = [
        'name',
        'code',
        'hex',
        'image',
        'added_by'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}
