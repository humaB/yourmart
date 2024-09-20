<?php

namespace App\Models\Inventory\Courier;

use App\Http\Controllers\Inventory\Setting\CourierController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourierCategoryRange extends Model
{
    use HasFactory;

    protected $table = 'courier_categories_ranges';

    protected $fillable = [
        'category_id',
        'minimum_quantity',
        'maximum_quantity',
        'base_rate',
        'per_kg',
        'per_kg_rate',
        'fac_tax',
        'gst_tax',
        'total',
        'added_by'
    ];

    public function category(){
        return $this->belongsTo(CourierController::class, 'category_id', 'id');
    }

}
