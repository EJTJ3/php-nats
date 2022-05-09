<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Encoder;

final class JsonEncoder implements EncoderInterface
{
    public function encode(object|string|array $payload): string
    {
        return json_encode($payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function decode(string $payload): array
    {
        return json_decode($payload, true);
    }
}
