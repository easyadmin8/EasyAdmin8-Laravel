<?php

namespace App\Helpers;

class Utils
{
    public static function getIp(): string
    {
        return request()->ip();
    }

    public static function getUid(): int|string
    {
        return request()->session()->get('admin.id');
    }
}
