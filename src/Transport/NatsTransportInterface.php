<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport;

use EJTJ3\PhpNats\Constant\Nats;
use Nyholm\Dsn\Configuration\Url;
use Psl\DateTime\Duration;

interface NatsTransportInterface
{
    public function connect(Url $url, Duration $timeout): void;

    public function close(): void;

    public function isClosed(): bool;

    public function enableTls(): void;

    public function isConnected(): bool;

    public function write(string $payload): void;

    public function read(string $lineEnding = Nats::CR_LF): ?string;
}
