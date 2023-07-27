<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

interface MessageInterface extends NatsResponseInterface
{
    public function getPayload(): string;
}
