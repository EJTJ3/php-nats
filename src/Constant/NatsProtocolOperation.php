<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Constant;

/**
 * @author Evert Jan Hakvoort <evertjan@hakvoort.io>
 *
 * @see https://docs.nats.io/reference/reference-protocols/nats-protocol
 */
enum NatsProtocolOperation: string
{
    /**
     * Sent by server
     * Sent to client after initial TCP/IP connection.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#info
     */
    case Info = 'INFO';

    /**
     * Sent to client after initial TCP/IP connection.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#connect
     */
    case Connect = 'CONNECT';

    /**
     * Sent by client
     * Publish a message to a subject, with optional reply subject.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#pub
     */
    case Pub = 'PUB';

    /**
     * Sent by client
     * Subscribe to a subject (or subject wildcard).
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#sub
     */
    case Sub = 'SUB';

    /**
     * Sent by client
     * Unsubscribe (or auto-unsubscribe) from subject.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#unsub
     */
    case Unsub = 'UNSUB';

    /**
     * Sent by server
     * Delivers a message payload to a subscriber.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#msg
     */
    case Msg = 'MSG';

    /**
     * PING keep-alive message.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#pingpong
     */
    case Ping = 'PING';

    /**
     * PONG keep-alive response.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#pingpong
     */
    case Pong = 'PONG';

    /**
     * Sent by Server
     * Acknowledges well-formed protocol message in verbose mode.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#okerr
     */
    case Ack = '+OK';

    /**
     * Sent by Server
     * Indicates a protocol error. May cause client disconnect.
     *
     * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#okerr
     */
    case Err = '-ERR';

    public function isOperation(string $value): bool
    {
        return $this->value === $value;
    }
}
