<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

interface NatsConnectionOptionInterface
{
    public function getServerCollection(): ServerCollection;

    public function getTimeout(): int;

    public function getName(): ?string;
}
