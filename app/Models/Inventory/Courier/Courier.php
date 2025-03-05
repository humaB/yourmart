<?php

namespace App\Models\Inventory\Courier;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory;

    protected $fillable = [
        'courier_name',
        'contact_person',
        'contact_person_contact',
        'added_by'
    ];

    public function categories(){
        return $this->hasMany(CourierCategory::class, 'courier_id', 'id');
    }

    public function disclaimer(){
        return $this->hasOne(CourierDisclaimer::class, 'courier_id', 'id');
    }
}
