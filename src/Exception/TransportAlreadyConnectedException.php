<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Exception;

use LogicException;

final class TransportAlreadyConnectedException extends LogicException implements NatsExceptionInterface
{
}
