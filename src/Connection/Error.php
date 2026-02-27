<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

final class Error implements NatsResponseInterface
{
    public function __construct(
        private readonly string $message = '',
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
