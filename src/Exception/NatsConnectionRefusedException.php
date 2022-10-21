<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Exception;

use RuntimeException;

final class NatsConnectionRefusedException extends RuntimeException implements NatsExceptionInterface
{
}
