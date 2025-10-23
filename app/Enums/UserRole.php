<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case SUPER_ADMIN = 'super_admin';
    case VENDOR = 'vendor';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
