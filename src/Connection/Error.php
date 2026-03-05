<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

final class Error implements NatsResponseInterface
{
    public function __construct(public string $message)
    {
    }

    public static function parse(string $payload): self
    {
        return new self(substr($payload, 6, -1));
    }
}
