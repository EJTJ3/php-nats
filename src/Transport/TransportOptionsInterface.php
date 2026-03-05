<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Transport;

use Nyholm\Dsn\Configuration\Url;
use Psl\DateTime\Duration;

interface TransportOptionsInterface
{
    public function getUrl(): Url;

    public function getTimeout(): Duration;
}
