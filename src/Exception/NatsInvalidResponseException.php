<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Exception;

use RuntimeException;

final class NatsInvalidResponseException extends RuntimeException implements NatsExceptionInterface
{
}
