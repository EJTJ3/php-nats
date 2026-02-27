<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\ServerInfo;
use PHPUnit\Framework\TestCase;

final class ServerInfoTest extends TestCase
{
    public function testFromDataMinimal(): void
    {
        $data = json_encode([
            'server_id' => 'test-server',
            'version' => '2.9.0',
            'host' => '0.0.0.0',
            'port' => 4222,
            'proto' => 1,
            'max_payload' => 1048576,
        ]);

        $info = ServerInfo::fromData($data);

        $this->assertSame('test-server', $info->getServerId());
        $this->assertSame('2.9.0', $info->getVersion());
        $this->assertNull($info->getGoVersion());
        $this->assertSame('0.0.0.0', $info->getHost());
        $this->assertSame(4222, $info->getPort());
        $this->assertSame(1, $info->getProto());
        $this->assertSame(1048576, $info->getMaxPayload());
        $this->assertFalse($info->isTlsRequired());
        $this->assertFalse($info->isTlsVerified());
        $this->assertFalse($info->isAuthRequired());
        $this->assertSame([], $info->getConnectUrls());
        $this->assertNull($info->getClientId());
        $this->assertFalse($info->isLameDuckMode());
    }

    public function testFromDataFull(): void
    {
        $data = json_encode([
            'server_id' => 'NATS-123',
            'version' => '2.10.0',
            'go' => 'go1.21',
            'host' => '127.0.0.1',
            'port' => 4223,
            'proto' => 1,
            'max_payload' => 8388608,
            'tls_required' => true,
            'tls_verify' => true,
            'auth_required' => true,
            'connect_urls' => ['nats://server1:4222', 'nats://server2:4222'],
            'client_id' => 42,
            'ldm' => true,
        ]);

        $info = ServerInfo::fromData($data);

        $this->assertSame('NATS-123', $info->getServerId());
        $this->assertSame('2.10.0', $info->getVersion());
        $this->assertSame('go1.21', $info->getGoVersion());
        $this->assertSame('127.0.0.1', $info->getHost());
        $this->assertSame(4223, $info->getPort());
        $this->assertSame(1, $info->getProto());
        $this->assertSame(8388608, $info->getMaxPayload());
        $this->assertTrue($info->isTlsRequired());
        $this->assertTrue($info->isTlsVerified());
        $this->assertTrue($info->isAuthRequired());
        $this->assertSame(['nats://server1:4222', 'nats://server2:4222'], $info->getConnectUrls());
        $this->assertSame(42, $info->getClientId());
        $this->assertTrue($info->isLameDuckMode());
    }
}
