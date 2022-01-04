<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\Server;
use EJTJ3\PhpNats\Connection\ServerCollection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ServerCollectionTest extends TestCase
{
    public function testServerCollection()
    {
        $servers = [
            new Server('nats://admin:admin@nats3.example.com:4222'),
            new Server('nats://admin:admin@nats2.example.com:4222'),
            new Server('nats://admin:admin@nats1.example.com:4222'),
        ];

        $collection = new ServerCollection($servers);

        $this->assertSame($servers, $collection->getServers());
    }

    public function testEmptyServers(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ServerCollection([]);
    }
}
