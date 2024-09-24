<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LibraryPageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'attachment',
        'video_links',
        'added_by',
    ];

    protected $casts = [
        'video_links' => 'array',
    ];

    public function added_name()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
