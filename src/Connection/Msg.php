<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use EJTJ3\PhpNats\Util\StringUtil;
use InvalidArgumentException;

final class Msg implements MessageInterface
{
    /**
     * @var string the message payload data
     */
    private string $payload;

    private function __construct(
        // subject name this message was received on - always
        public readonly string $subject,
        // The unique alphanumeric subscription ID of the subject. - always
        public readonly string $sid,
        // The subject on which the publisher is listening for responses. - optional
        public readonly ?string $replyTo,
        // Size of the payload in bytes.
        public readonly int $bytes,
    ) {
        $this->payload = '';
    }

    /**
     * @param string $protocolMessage MSG <subject> <sid> [reply-to] <#bytes>␍␊[payload]␍␊
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#syntax-6
     */
    public static function create(string $protocolMessage): self
    {
        $parts = StringUtil::explode($protocolMessage, 4);

        return match (count($parts)) {
            3 => new self($parts[0], $parts[1], null, (int) $parts[2]),
            4 => new self($parts[0], $parts[1], $parts[2], (int) $parts[3]),
            default => throw new InvalidArgumentException('Invalid format'),
        };
    }

    public function getPayload(): string
    {
        return $this->payload ?? '';
    }

    public function setPayload(string $payload): void
    {
        $this->payload = $payload;
    }
}
