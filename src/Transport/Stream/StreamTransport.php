<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport\Stream;

use EJTJ3\PhpNats\Constant\Nats;
use EJTJ3\PhpNats\Exception\NatsConnectionRefusedException;
use EJTJ3\PhpNats\Transport\NatsTransportInterface;
use EJTJ3\PhpNats\Transport\TransportOptionsInterface;
use Psl\DateTime\Duration;
use Psl\IO\Reader;
use Psl\Network\Exception\RuntimeException as NetworkRuntimeException;
use Psl\Network\StreamSocketInterface;
use Psl\TCP;

final class StreamTransport implements NatsTransportInterface
{
    private ?StreamSocketInterface $socket = null;

    private ?Reader $reader = null;

    public function close(): void
    {
        $this->getSocket()->close();
        $this->socket = null;
        $this->reader = null;
    }

    public function isClosed(): bool
    {
        return $this->socket === null;
    }

    public function isConnected(): bool
    {
        return $this->socket !== null;
    }

    public function connect(TransportOptionsInterface $option): void
    {
        $host = $option->getHost();
        if ($host === '') {
            throw new NatsConnectionRefusedException('Host cannot be empty');
        }

        $port = $option->getPort() ?? 4222;
        if ($port < 0) {
            throw new NatsConnectionRefusedException('Port must be a non-negative integer');
        }

        try {
            $socket = TCP\connect(
                $host,
                $port,
                timeout: Duration::seconds($option->getTimeout()),
            );
        } catch (NetworkRuntimeException $e) {
            throw new NatsConnectionRefusedException(
                sprintf(
                    'Could not connect to %s:%d with a timeout of %d seconds',
                    $host,
                    $port,
                    $option->getTimeout(),
                ),
                previous: $e,
            );
        }

        $this->socket = $socket;
        $this->reader = new Reader($socket);
    }

    public function enableTls(): void
    {
        $resource = $this->getSocket()->getStream();

        if (!is_resource($resource)) {
            throw new NatsConnectionRefusedException('Stream does not exist, try reconnecting');
        }

        // PSL operates in non-blocking mode; TLS handshake requires blocking mode.
        stream_set_blocking($resource, true);

        set_error_handler(static function (int $errorCode, string $errorMessage): bool {
            restore_error_handler();
            throw new NatsConnectionRefusedException(sprintf('Failed to enable TLS: %s', $errorMessage));
        });

        $result = stream_socket_enable_crypto($resource, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);

        restore_error_handler();

        stream_set_blocking($resource, false);

        if ($result !== true) {
            throw new NatsConnectionRefusedException('Failed to enable TLS: Error negotiating crypto');
        }
    }

    public function write(string $payload): void
    {
        if (strlen($payload) === 0) {
            return;
        }

        try {
            $this->getSocket()->writeAll($payload);
        } catch (\Throwable $e) {
            throw new NatsStreamWriteException('Error sending data', previous: $e);
        }
    }

    public function read(int $length, string $lineEnding = Nats::CR_LF): string
    {
        $data = $this->getReader()->readUntil($lineEnding);

        if ($data === null) {
            throw new NatsConnectionRefusedException('Connection closed while reading');
        }

        return $data;
    }

    private function getSocket(): StreamSocketInterface
    {
        if ($this->socket === null) {
            throw new NatsConnectionRefusedException('Stream does not exist, try reconnecting');
        }

        return $this->socket;
    }

    private function getReader(): Reader
    {
        if ($this->reader === null) {
            throw new NatsConnectionRefusedException('Stream does not exist, try reconnecting');
        }

        return $this->reader;
    }
}
