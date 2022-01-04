<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Encoder;

/**
 * @author Evert Jan Hakvoort <evertjan@hakvoort.io>
 */
interface EncoderInterface
{
    /**
     * @param mixed $payload
     */
    public function encode($payload): string;

    /**
     * @return mixed
     */
    public function decode(string $payload);
}
