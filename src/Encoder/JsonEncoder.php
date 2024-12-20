<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Encoder;

use JsonException;

final class JsonEncoder implements EncoderInterface
{
    /**
     * @throws JsonException
     */
    public function encode(object|string|array $payload): string
    {
        return json_encode($payload, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<mixed>
     */
    public function decode(string $payload): array
    {
        // @phpstan-ignore-next-line
        return json_decode($payload, true);
    }
}
