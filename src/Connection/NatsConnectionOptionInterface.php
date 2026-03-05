<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use Psl\DateTime\Duration;

interface NatsConnectionOptionInterface
{
    public function getServerCollection(): ServerCollection;

    public function getTimeout(): Duration;

    public function getName(): ?string;
}
