<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport;

interface TransportOptionsInterface
{
    public function getTimeout(): int;

    public function getHost(): string;

    public function getPort(): ?int;
}
