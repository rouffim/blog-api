<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Storage;

class UtilsHelper
{
    /**
     * @param $bool
     * @return string|null
     */
    static function isBoolean($bool): ?string
    {
        return is_bool($bool) || (is_string($bool) && ($bool == 'true' || $bool == 'false'));
    }
}
