<?php

namespace App\Models\Inventory\Product\Variation;

use App\Models\Inventory\Product\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTag extends Model
{
    use HasFactory;

    protected $table = 'inventory_product_added_tags';

    protected $fillable = [
        'product_id',
        'tag_id',
        'added_by',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function tag(){
        return $this->belongsTo(Tag::class, 'tag_id', 'id');
    }
}
