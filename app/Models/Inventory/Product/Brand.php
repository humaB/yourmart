<?php

namespace App\Models\Inventory\Product;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_brands';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'added_by'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
}
