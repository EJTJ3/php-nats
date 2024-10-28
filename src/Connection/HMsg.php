<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use EJTJ3\PhpNats\Util\StringUtil;
use Exception;
use InvalidArgumentException;

final class HMsg implements MessageInterface
{
    private array $headers;

    public ?string $payload;

    public ?string $protocol;

    public function __construct(
        // Subject name this message was received on.
        public string $subject,
        // The unique alphanumeric subscription ID of the subject.
        public string $subscriptionId,
        // The subject on which the publisher is listening for responses.
        public ?string $replyTo,
        // The size of the headers section in bytes including the ␍␊␍␊ delimiter before the payload.
        public int $headerBytes,
        // The total size of headers and payload sections in bytes.
        public int $totalBytes,
    ) {
        $this->headers = [];
        $this->payload = null;
    }

    public function getPayload(): string
    {
        return $this->payload ?? '';
    }

    public function getHeader(string $key): string|int|null
    {
        return $this->headers[$key] ?? null;
    }

    public function setPayload(string $payload): void
    {
        $this->payload = $payload;
    }

    public function setHeaders(string $headers): void
    {
        // Check if we have an inlined status.
        $parts = explode(' ', $headers);

        if (isset($parts[1]) && strlen($parts[1]) === 3) {
            $this->headers['status'] = (int) $parts[1];
        }
    }

    // HMSG <subject> <sid> [reply-to] <#header bytes> <#total bytes>␍␊[headers]␍␊␍␊[payload]␍␊
    public static function create(string $protocolMessage): HMsg
    {
        $parts = StringUtil::explode($protocolMessage, 5);

        try {
            return match (count($parts)) {
                4 => new self($parts[0], $parts[1], null, (int) $parts[2], (int) $parts[3]),
                5 => new self($parts[0], $parts[1], $parts[2], (int) $parts[3], (int) $parts[4]),
                default => throw new InvalidArgumentException('Invalid msg'),
            };
        } catch (Exception $e) {
            // Add own exception
            throw $e;
        }
    }
}
