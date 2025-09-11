<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProductAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'attachment'
    ];
}
