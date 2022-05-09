<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport;

final class TranssportOption implements TransportOptionsInterface
{
    public function __construct(
        private string $host,
        private int $port,
        private int $timeout
    ) {
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }
}
