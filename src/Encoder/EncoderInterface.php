<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Encoder;

/**
 * @author Evert Jan Hakvoort <evertjan@hakvoort.io>
 */
interface EncoderInterface
{
    public function encode(object|string|array $payload): string;

    public function decode(string $payload): object|array;
}
