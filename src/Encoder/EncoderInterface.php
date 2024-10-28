<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Encoder;

interface EncoderInterface
{
    /**
     * @param object|string|array<string, mixed> $payload
     */
    public function encode(object|string|array $payload): string;

    /**
     * @return array<string, mixed>
     */
    public function decode(string $payload): object|array;
}
