<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport\Stream;

use EJTJ3\PhpNats\Constant\Nats;
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

    public function __construct(
        /**
         * @var int<0, max> $chunkSize
         */
        private readonly int $chunkSize = 1024,
    ) {
        $this->stream = null;
    }

    /**
     * Close will close the connection to the server.
     */
    public function close(): void
    {
        fclose($this->getStream());
        $this->stream = null;
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

        set_error_handler(static fn () => true);

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
            STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
        )) {
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

        if ($length === 0) {
            return;
        }

        do {
            $written = @fwrite($this->getStream(), $payload, $this->chunkSize);

            if ($written === false) {
                throw new NatsStreamWriteException('Error sending data');
            }

            if ($written === 0) {
                throw new NatsStreamWriteException('Broken pipe or closed connection');
            }

            $length -= $written;

            if ($length > 0) {
                $payload = substr($payload, 0 - $length);
            }
        } while ($length > 0);
    }

    public function read(int $length, string $lineEnding = Nats::CR_LF): false|string
    {
        return stream_get_line($this->getStream(), $length, $lineEnding);
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
