<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Constant;

/**
 * @author Evert Jan Hakvoort <evertjan@hakvoort.io>
 *
 * @see https://docs.nats.io/reference/reference-protocols/nats-protocol
 */
final class NatsProtocolOperation
{
    /**
     * Sent by server
     * Sent to client after initial TCP/IP connection.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#info
     */
    public const INFO = 'INFO';

    /**
     * Sent to client after initial TCP/IP connection.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#connect
     */
    public const CONNECT = 'CONNECT';

    /**
     * Sent by client
     * Publish a message to a subject, with optional reply subject.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#pub
     */
    public const PUB = 'PUB';

    /**
     * Sent by client
     * Subscribe to a subject (or subject wildcard).
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#sub
     */
    public const SUB = 'SUB';

    /**
     * Sent by client
     * Unsubscribe (or auto-unsubscribe) from subject.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#unsub
     */
    public const UNSUB = 'UNSUB';

    /**
     * Sent by server
     * Delivers a message payload to a subscriber.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#msg
     */
    public const MSG = 'MSG';

    /**
     * PING keep-alive message.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#pingpong
     */
    public const PING = 'PING';

    /**
     * PONG keep-alive response.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#pingpong
     */
    public const PONG = 'PONG';

    /**
     * Sent by Server
     * Acknowledges well-formed protocol message in verbose mode.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#okerr
     */
    public const ACK = '+OK';

    /**
     * Sent by Server
     * Indicates a protocol error. May cause client disconnect.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#okerr
     */
    public const ERR = '-ERR';

    public const AVAILABLE_OPERATIONS = [
        self::INFO,
        self::CONNECT,
        self::PUB,
        self::SUB,
        self::UNSUB,
        self::MSG,
        self::PING,
        self::PONG,
        self::ACK,
        self::ERR,
    ];
}
