<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomePageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // Image || Tag
        'tag_id',
        'attachment',
        'position', // In Case of Image this will save Button Link
        'added_by',
    ];
}
