<?php

namespace App\Models\Inventory\Product\Variation;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDimension extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_dimensions';

    protected $fillable = [
        'product_id',
        'weight',
        'length',
        'height',
        'width',
        'added_by',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

}
