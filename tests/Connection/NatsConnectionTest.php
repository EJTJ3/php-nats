<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\NatsConnection;
use EJTJ3\PhpNats\Connection\NatsConnectionOption;
use EJTJ3\PhpNats\Exception\NatsConnectionRefusedException;
use EJTJ3\PhpNats\Transport\NatsTransportInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class NatsConnectionTest extends TestCase
{
    public function testConnectSuccessfully(): void
    {
        $serverInfoJson = json_encode([
            'server_id' => 'test',
            'version' => '2.0.0',
            'host' => '0.0.0.0',
            'port' => 4222,
            'proto' => 1,
            'max_payload' => 1048576,
        ]);

        $transport = $this->createMockTransport([
            'INFO ' . $serverInfoJson, // server info
            'PONG',                     // ping response
        ]);

        $options = new NatsConnectionOption('nats://localhost:4222');
        $connection = new NatsConnection($options, $transport);

        $connection->connect();

        $this->assertTrue($connection->isConnected());
        $this->assertNotNull($connection->getServerInfo());
        $this->assertSame('test', $connection->getServerInfo()->getServerId());
    }

    public function testConnectAlreadyConnected(): void
    {
        $transport = $this->createMock(NatsTransportInterface::class);
        $transport->method('isConnected')->willReturn(true);

        $options = new NatsConnectionOption('nats://localhost:4222');
        $connection = new NatsConnection($options, $transport);

        // Should return early without error
        $connection->connect();

        $this->assertFalse($connection->isConnected());
    }

    public function testConnectNoServersAvailable(): void
    {
        $transport = $this->createMock(NatsTransportInterface::class);
        $transport->method('isConnected')->willReturn(false);
        $transport->method('connect')->willThrowException(
            new NatsConnectionRefusedException('Connection refused')
        );

        $options = new NatsConnectionOption('nats://localhost:4222');
        $connection = new NatsConnection($options, $transport);

        $this->expectException(NatsConnectionRefusedException::class);
        $this->expectExceptionMessage('nats: no servers available for connection');

        $connection->connect();
    }

    public function testPublishWhenNotConnectedThrows(): void
    {
        $transport = $this->createMock(NatsTransportInterface::class);

        $options = new NatsConnectionOption('nats://localhost:4222');
        $connection = new NatsConnection($options, $transport);

        $this->expectException(NatsConnectionRefusedException::class);
        $this->expectExceptionMessage('Connection is closed');

        $connection->publish('test', 'payload');
    }

    public function testSetNoRespondersWhenConnectedThrows(): void
    {
        $serverInfoJson = json_encode([
            'server_id' => 'test',
            'version' => '2.0.0',
            'host' => '0.0.0.0',
            'port' => 4222,
            'proto' => 1,
            'max_payload' => 1048576,
        ]);

        $transport = $this->createMockTransport([
            'INFO ' . $serverInfoJson,
            'PONG',
        ]);

        $options = new NatsConnectionOption('nats://localhost:4222');
        $connection = new NatsConnection($options, $transport);
        $connection->connect();

        $this->expectException(InvalidArgumentException::class);

        $connection->setNoResponders(true);
    }

    public function testSetVerboseWhenConnectedThrows(): void
    {
        $serverInfoJson = json_encode([
            'server_id' => 'test',
            'version' => '2.0.0',
            'host' => '0.0.0.0',
            'port' => 4222,
            'proto' => 1,
            'max_payload' => 1048576,
        ]);

        $transport = $this->createMockTransport([
            'INFO ' . $serverInfoJson,
            'PONG',
        ]);

        $options = new NatsConnectionOption('nats://localhost:4222');
        $connection = new NatsConnection($options, $transport);
        $connection->connect();

        $this->expectException(InvalidArgumentException::class);

        $connection->setVerbose(true);
    }

    public function testClose(): void
    {
        $serverInfoJson = json_encode([
            'server_id' => 'test',
            'version' => '2.0.0',
            'host' => '0.0.0.0',
            'port' => 4222,
            'proto' => 1,
            'max_payload' => 1048576,
        ]);

        $transport = $this->createMockTransport([
            'INFO ' . $serverInfoJson,
            'PONG',
        ]);

        $options = new NatsConnectionOption('nats://localhost:4222');
        $connection = new NatsConnection($options, $transport);
        $connection->connect();

        $this->assertTrue($connection->isConnected());

        $connection->close();

        $this->assertFalse($connection->isConnected());
    }

    /**
     * @param string[] $responses
     */
    private function createMockTransport(array $responses): NatsTransportInterface
    {
        $responseIndex = 0;
        $connected = false;

        $transport = $this->createMock(NatsTransportInterface::class);

        $transport->method('isConnected')->willReturnCallback(
            static function () use (&$connected): bool {
                return $connected;
            }
        );

        $transport->method('connect')->willReturnCallback(
            static function () use (&$connected): void {
                $connected = true;
            }
        );

        $transport->method('close')->willReturnCallback(
            static function () use (&$connected): void {
                $connected = false;
            }
        );

        $transport->method('read')->willReturnCallback(
            static function () use ($responses, &$responseIndex): string|false {
                if ($responseIndex >= count($responses)) {
                    return false;
                }

                return $responses[$responseIndex++];
            }
        );

        $transport->method('write');

        return $transport;
    }
}
