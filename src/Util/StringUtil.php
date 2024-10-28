<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Util;

final class StringUtil
{
    public static function isEmpty(?string $value): bool
    {
        return trim($value ?? '') === '';
    }

    /**
     * @return array<string>
     */
    public static function explode(string $string, ?int $limit = null): array
    {
        if ($limit === null) {
            $parts = explode(' ', $string);
        } else {
            $parts = explode(' ', $string, $limit);
        }

        return array_values(array_filter(
            $parts,
            static fn (?string $part) => !StringUtil::isEmpty($part))
        );
    }
}
