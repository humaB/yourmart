<?php

namespace App\Models\Inventory\Courier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_id',
        'name',
        'internal_label',
        'our_charges',
        'description',
        'added_by'
    ];

    public function ranges(){
        return $this->hasMany(CourierCategoryRange::class, 'category_id', 'id');
    }

}
