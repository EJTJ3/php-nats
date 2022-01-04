<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Exception;

use EJTJ3\PhpNats\Constant\NatsProtocolOperation;
use InvalidArgumentException;

final class NatsInvalidOperationException extends InvalidArgumentException implements NatsExceptionInterface
{
    public static function withOperation(string $operation): self
    {
        $message = sprintf(
            "Operation '%s' is not allowed, available operations are: %s",
            $operation,
            implode(', ', NatsProtocolOperation::AVAILABLE_OPERATIONS)
        );

        return new NatsInvalidOperationException($message);
    }
}
