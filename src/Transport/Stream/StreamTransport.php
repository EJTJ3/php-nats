<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport\Stream;

use EJTJ3\PhpNats\Exception\NatsConnectionRefusedException;
use EJTJ3\PhpNats\Transport\NatsTransportInterface;
use EJTJ3\PhpNats\Transport\TransportOptionsInterface;
use Exception;

final class StreamTransport implements NatsTransportInterface
{
    /**
     * @var resource|null
     */
    private $stream;

    /**
     * @var int<0, max>,
     */
    private int $chunkSize;

    /**
     * Close will close the connection to the server.
     */
    public function close(): void
    {
        fclose($this->getStream());
        $this->stream = null;
    }

    /**
     * @param int<0, max> $chunkSize
     */
    public function __construct(int $chunkSize = 1500)
    {
        $this->stream = null;
        $this->chunkSize = $chunkSize;
    }

    public function isClosed(): bool
    {
        return $this->stream === null;
    }

    public function isConnected(): bool
    {
        return $this->stream !== null;
    }

    public function connect(TransportOptionsInterface $option): void
    {
        // Params
        $address = sprintf('%s:%d', $option->getHost(), $option->getPort());
        $timeout = $option->getTimeout();
        $context = stream_context_get_default();

        // Create stream
        $errorCode = null;
        $errorMessage = null;

        set_error_handler(static fn() => true);

        $stream = stream_socket_client(
            $address,
            $errorCode,
            $errorMessage,
            $timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        restore_error_handler();

        if ($stream === false) {
            throw new NatsConnectionRefusedException(sprintf('Could not connect to %s with an timeout of %d secondes', $address, $timeout));
        }

        stream_set_timeout($stream, $timeout, 0);

        $this->stream = $stream;
    }

    public function enableTls(): void
    {
        // Create stream
        set_error_handler(static function ($errorCode, $errorMessage) {
            restore_error_handler();

            throw new NatsConnectionRefusedException(sprintf('Failed to connect: %s', $errorMessage));
        });

        if (!stream_socket_enable_crypto(
            $this->getStream(),
            true,
            STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)
        ) {
            throw new NatsConnectionRefusedException('Failed to connect: Error negotiating crypto');
        }

        restore_error_handler();
    }

    /**
     * @throws Exception
     */
    public function write(string $payload): void
    {
        $length = strlen($payload);

        while (true) {
            $written = @fwrite($this->getStream(), $payload);

            if ($written === false) {
                throw new NatsStreamWriteException('Error sending data');
            }

            if ($written === 0) {
                throw new NatsStreamWriteException('Broken pipe or closed connection');
            }

            $length -= $written;

            if ($length > 0) {
                $payload = substr($payload, (0 - $length));
            } else {
                break;
            }
        }
    }

    public function receive(int $length = 0): string
    {
        if ($length > 0) {
            $chunkSize = $this->chunkSize;
            $line = null;
            $receivedBytes = 0;

            while ($receivedBytes < $length) {
                $bytesLeft = ($length - $receivedBytes);

                if ($bytesLeft < $this->chunkSize) {
                    $chunkSize = $bytesLeft;
                }

                $readChunk = fread($this->getStream(), $chunkSize);
                $receivedBytes += strlen($readChunk);
                $line .= $readChunk;
            }
        } else {
            $line = fgets($this->getStream());
        }

        return $line;
    }

    /**
     * @return resource
     */
    private function getStream()
    {
        if ($this->stream === null) {
            throw new NatsConnectionRefusedException('Stream does not exist, try reconnecting');
        }

        return $this->stream;
    }
}
