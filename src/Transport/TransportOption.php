<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport;

use Nyholm\Dsn\Configuration\Url;
use Psl\DateTime\Duration;

final readonly class TransportOption implements TransportOptionsInterface
{
    public function __construct(
        private Url $url,
        private Duration $timeout,
    ) {
    }

    public function getTimeout(): Duration
    {
        return $this->timeout;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }
}
