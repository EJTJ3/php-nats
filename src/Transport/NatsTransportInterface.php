<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport;

interface NatsTransportInterface
{
    public function connect(TransportOptionsInterface $option): void;

    public function close(): void;

    public function isClosed(): bool;

    public function enableTls(): void;

    public function isConnected(): bool;

    public function write(string $payload): void;

    public function receive(int $length = 0): bool|string;
}
