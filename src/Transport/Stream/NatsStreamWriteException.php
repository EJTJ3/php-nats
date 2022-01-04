<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport\Stream;

use EJTJ3\PhpNats\Exception\NatsExceptionInterface;
use RuntimeException;

final class NatsStreamWriteException extends RuntimeException implements NatsExceptionInterface
{
}
