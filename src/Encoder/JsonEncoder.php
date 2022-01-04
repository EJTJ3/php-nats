<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Encoder;

final class JsonEncoder implements EncoderInterface
{
    /**
     * @param mixed $payload
     */
    public function encode($payload): string
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
