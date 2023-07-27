<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Constant;

abstract class Nats
{
    final public const DEFAULT_PORT = 4222;

    final public const CR_LF = "\r\n";

    final public const LANG = 'php';

    final public const VERSION = '0.0.2';

    final public const HEADER_LINE = 'NATS/1.0';

    final public const HEADER_STATUS_LENGTH = 3;

    final public const HEADER_NO_RESPONDER = 503;
}
