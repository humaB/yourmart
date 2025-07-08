<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', // Order || Pay-outs || New Arrival
        'message',
        'link',
        'image',
        'color',
        'is_public', // 0 => Public || 1 => Specific User
        'user_id',
    ];
}
