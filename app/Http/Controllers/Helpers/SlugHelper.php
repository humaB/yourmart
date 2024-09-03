<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SlugHelper
{
    public static function generateSlug($string)
    {
        return Str::slug($string, '-');
    }
}
