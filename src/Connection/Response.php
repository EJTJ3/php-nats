<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use EJTJ3\PhpNats\Constant\NatsProtocolOperation;
use LogicException;
use RuntimeException;

final class Response
{
    public static function parse(string $payload): NatsResponseInterface
    {
        if (NatsProtocolOperation::Pong->isOperation($payload)) {
            return new Pong();
        }

        if (NatsProtocolOperation::Ping->isOperation($payload)) {
            return new Ping();
        }

        if (NatsProtocolOperation::Ack->isOperation($payload)) {
            return new Acknowledgement();
        }

        if (NatsProtocolOperation::Err->isOperation($payload)) {
            return new Error();
        }

        if (!str_contains($payload, ' ')) {
            throw new RuntimeException('Invalid response format');
        }

        [$responseType, $body] = explode(' ', $payload, 2);

        $responseType = strtoupper($responseType);

        $operation = NatsProtocolOperation::tryFrom($responseType);

        if ($operation === null) {
            throw new RuntimeException('Operation is not supported');
        }

        return match ($operation) {
            NatsProtocolOperation::HEADER_MSG => HMsg::create($body),
            NatsProtocolOperation::Msg => Msg::create($body),
            NatsProtocolOperation::Info => \EJTJ3\PhpNats\Connection\ServerInfo::fromData($body),
            default => throw new LogicException('Not implemented yet')
        };
    }
}
