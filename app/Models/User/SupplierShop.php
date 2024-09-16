<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierShop extends Model
{
    use HasFactory;

    protected $table = 'supplier_shops';

    protected $fillable = [
        'supplier_id',
        'store_name',
        'store_type',/*[
            'Physical Shop',
            'Manufacturer',
            'Importer',
            'Online Seller',
            'Dropshipper',
        ] */
        'store_url',
        'social_media_profile_link',
        'store_type',
        'business_description',
        'product_description',
        'comment',
    ];
}
