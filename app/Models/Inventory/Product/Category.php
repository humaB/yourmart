<?php

namespace App\Models\Inventory\Product;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id', // Self Join
        'added_by'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function parent(){
        return $this->hasOne(Category::class, 'id', 'parent_id');
    }
}
