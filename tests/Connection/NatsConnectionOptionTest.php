<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\NatsConnectionOption;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NatsConnectionOptionTest extends TestCase
{
    public function testMultipleServers(): void
    {
        $servers = 'nats://admin:admin@example.com,nats://admin:admin@examples.com';
        $servers = new NatsConnectionOption($servers);

        $this->assertCount(2, $servers->getServerCollection()->getServers());
    }

    public function testSingleServer(): void
    {
        $servers = 'nats://admin:admin@example.com';
        $servers = new NatsConnectionOption($servers);

        $this->assertCount(1, $servers->getServerCollection()->getServers());
    }

    public function testEmptyServer(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new NatsConnectionOption('');
    }

    public function testGetters(): void
    {
        $options = new NatsConnectionOption(
            'nats://admin:admin@example.com:4222',
            'Testing server',
            15
        );

        $this->assertSame('Testing server', $options->getName());
        $this->assertSame(15, $options->getTimeout());
    }
}
