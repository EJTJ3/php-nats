<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Util;

final class StringUtil
{
    public static function isEmpty(?string $value): bool
    {
        return trim($value ?? '') === ';
    }
}
