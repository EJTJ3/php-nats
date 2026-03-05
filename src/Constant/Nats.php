<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Constant;

abstract class Nats
{
    final public const int DEFAULT_PORT = 4222;

    final public const string CR_LF = "\r\n";

    final public const string LANG = 'php';

    final public const string VERSION = '0.0.2';

    final public const string HEADER_LINE = 'NATS/1.0';

    final public const int HEADER_STATUS_LENGTH = 3;

    final public const int HEADER_NO_RESPONDER = 503;
}
