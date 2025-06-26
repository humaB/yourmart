<?php

namespace App\Models\Setting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'type' , //dropshipper_approved,dropshipper_reject,dropshipper_received
        'subject',
        'body',
        'added_by'
    ];

    public function added_name()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
