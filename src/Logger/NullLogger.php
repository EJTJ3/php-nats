<?php

namespace EJTJ3\PhpNats\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

final class NullLogger extends AbstractLogger implements LoggerInterface
{
    public function log($level, $message, array $context = []): void
    {
    }
}
