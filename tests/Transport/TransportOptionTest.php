<?php

declare(strict_types=1);

namespace Transport;

use EJTJ3\PhpNats\Transport\TransportOption;
use Nyholm\Dsn\DsnParser;
use PHPUnit\Framework\TestCase;
use Psl\DateTime\Duration;

final class TransportOptionTest extends TestCase
{
    public function testTransportOption(): void
    {
        $option = new TransportOption(
            DsnParser::parseUrl('nats://host:9222'),
            Duration::seconds(5),
        );

        $this->assertSame(5.0, $option->getTimeout()->getTotalSeconds());
        $this->assertNotNull($option->getUrl());
    }
}
