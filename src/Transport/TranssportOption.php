<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport;

final class TranssportOption implements TransportOptionsInterface
{
    private int $timeout;

    private string $host;

    private int $port;

    public function __construct(string $host, int $port, int $timeout)
    {
        $this->timeout = $timeout;
        $this->host = $host;
        $this->port = $port;
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
