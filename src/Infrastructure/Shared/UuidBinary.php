<?php

namespace Testcenter\Infrastructure\Shared;

final class UuidBinary
{
    public static function toBin(string $uuid): string
    {
        return hex2bin(str_replace('-', '', $uuid));
    }

    public static function toStr(string $bin): string
    {
        $hex = bin2hex($bin);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );
    }
}
