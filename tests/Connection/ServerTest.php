<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\Server;
use EJTJ3\PhpNats\Constant\Nats;
use Nyholm\Dsn\Configuration\Url;
use PHPUnit\Framework\TestCase;

final class ServerTest extends TestCase
{
    public function testServer(): void
    {
        $host = 'nats-server.example.com';
        $server = new Server($host);

        $this->assertSame($host, $server->getHost());
        $this->assertInstanceOf(Url::class, $server->getUrl());
        $this->assertEmpty($server->getUser());
        $this->assertEmpty($server->getPassword());
        $this->assertSame(Nats::DEFAULT_PORT, $server->getPort());
        $this->assertFalse($server->isTls());
    }

    public function testServerWithPort(): void
    {
        $host = 'nats-server.example.com:4222';
        $server = new Server($host);

        $this->assertSame('nats-server.example.com', $server->getHost());
        $this->assertSame(4222, $server->getPort());
        $this->assertEmpty($server->getUser());
        $this->assertEmpty($server->getPassword());
        $this->assertFalse($server->isTls());
    }

    public function testServerWithAuthentication(): void
    {
        $host = 'admin:admin123@nats-server.example.com:4222';
        $server = new Server($host);

        $this->assertSame('nats-server.example.com', $server->getHost());
        $this->assertSame(4222, $server->getPort());
        $this->assertSame('admin', $server->getUser());
        $this->assertSame('admin123', $server->getPassword());
        $this->assertFalse($server->isTls());
    }

    public function testServerWithAuthenticationWithoutPort(): void
    {
        $host = 'admin:admin123@nats-server.example.com';
        $server = new Server($host);

        $this->assertSame('nats-server.example.com', $server->getHost());
        $this->assertSame(Nats::DEFAULT_PORT, $server->getPort());
        $this->assertSame('admin', $server->getUser());
        $this->assertSame('admin123', $server->getPassword());
        $this->assertFalse($server->isTls());
    }

    public function testMultipleServers(): void
    {
        $host = 'nats://admin:admin123@nats-server.example.com:8888';
        $server = new Server($host);

        $this->assertSame('nats-server.example.com', $server->getHost());
        $this->assertSame(8888, $server->getPort());
        $this->assertSame('admin', $server->getUser());
        $this->assertSame('admin123', $server->getPassword());
        $this->assertSame('nats', $server->getScheme());
    }

    public function testServerWithScheme(): void
    {
        $host = 'tls://admin:admin123@nats-server.example.com:4222';

        $server = new Server($host);

        $this->assertSame('nats-server.example.com', $server->getHost());
        $this->assertSame(Nats::DEFAULT_PORT, $server->getPort());
        $this->assertSame('admin', $server->getUser());
        $this->assertSame('admin123', $server->getPassword());
        $this->assertSame('tls', $server->getScheme());
        $this->assertTrue($server->isTls());
    }
}
