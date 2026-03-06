<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport\Stream;

use EJTJ3\PhpNats\Constant\Nats;
use EJTJ3\PhpNats\Exception\NatsConnectionRefusedException;
use EJTJ3\PhpNats\Transport\NatsTransportInterface;
use Nyholm\Dsn\Configuration\Url;
use Psl\DateTime\Duration;
use Psl\IO\Reader;
use Psl\Network\StreamInterface;
use Psl\TCP;
use Psl\TLS;
use Psl\TLS\ClientConfig;
use Psl\TLS\Connector;

final class StreamTransport implements NatsTransportInterface
{
    private ?StreamInterface $stream;

    private ?Reader $reader;

    private ?Duration $timeout;

    /**
     * @var non-empty-string|null
     */
    private ?string $host;

    public function __construct()
    {
        $this->stream = null;
        $this->reader = null;
        $this->timeout = null;
        $this->host = null;
    }

    /**
     * Close will close the connection to the server.
     */
    public function close(): void
    {
        $this->stream?->close();
        $this->stream = null;
        $this->host = null;
        $this->timeout = null;
        $this->reader = null;
    }

    public function isClosed(): bool
    {
        return $this->stream === null;
    }

    public function isConnected(): bool
    {
        return $this->stream !== null;
    }

    public function connect(Url $url, Duration $timeout): void
    {
        $host = $url->getHost();

        if ($host === '') {
            throw new NatsConnectionRefusedException('Host cannot be empty');
        }

        $port = $url->getPort() ?? Nats::DEFAULT_PORT;
        if ($port < 0 || $port > 65535) {
            throw new NatsConnectionRefusedException('Port must be a non-negative integer');
        }

        $this->host = $host;
        $this->timeout = $timeout;
        $this->stream = TCP\connect($host, $port, timeout: $timeout);
        $this->reader = new Reader($this->stream);
    }

    public function write(string $payload): void
    {
        $this->getStream()->writeAll($payload, $this->timeout);
    }

    public function read(string $lineEnding = Nats::CR_LF): ?string
    {
        return $this->getReader()->readUntil($lineEnding, $this->timeout);
    }

    private function getReader(): Reader
    {
        if ($this->reader === null) {
            throw new NatsConnectionRefusedException('Stream does not exist, try reconnecting');
        }

        return $this->reader;
    }

    public function getStream(): StreamInterface
    {
        if ($this->stream === null) {
            throw new NatsConnectionRefusedException('Stream does not exist, try reconnecting');
        }

        return $this->stream;
    }

    public function enableTls(): void
    {
        $stream = $this->getStream();

        if ($stream instanceof TLS\StreamInterface) {
            return;
        }

        if ($this->host === null) {
            throw new NatsConnectionRefusedException('Host not found');
        }

        $this->stream = new Connector(ClientConfig::default())->connect($stream, $this->host);
        $this->reader = new Reader($this->stream);
    }
}
