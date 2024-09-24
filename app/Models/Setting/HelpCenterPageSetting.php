<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class HelpCenterPageSetting extends Model
{
    use HasFactory, SoftDeletes;

    // Define the table associated with the model (if different from the pluralized name)
    protected $table = 'help_center_page_settings';

    // Define the fillable attributes
    protected $fillable = [
        'name',
        'description',
        'type',
        'added_by',
    ];

    // Optionally, you can define the hidden attributes or casts
    protected $hidden = [
        // Add any attributes you want to hide when converting to arrays
    ];

    public function added_name()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
