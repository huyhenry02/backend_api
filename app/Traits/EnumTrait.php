<?php

namespace App\Traits;
trait EnumTrait
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::names(), self::values());
    }

    public static function name($value): string
    {
        return self::array()[$value];
    }

    public static function value($name): string
    {
        return self::array()[$name];
    }
}
