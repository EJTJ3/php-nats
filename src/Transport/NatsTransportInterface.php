<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport;

use EJTJ3\PhpNats\Constant\Nats;

interface NatsTransportInterface
{
    public function connect(TransportOptionsInterface $option): void;

    public function close(): void;

    public function isClosed(): bool;

    public function enableTls(): void;

    public function isConnected(): bool;

    public function write(string $payload): void;

    public function read(int $length, string $lineEnding = Nats::CR_LF): bool|string;
}
