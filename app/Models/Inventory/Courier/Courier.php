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
}
