<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use EJTJ3\PhpNats\Constant\Nats;
use EJTJ3\PhpNats\Constant\NatsProtocolOperation;
use EJTJ3\PhpNats\Encoder\EncoderInterface;
use EJTJ3\PhpNats\Encoder\JsonEncoder;
use EJTJ3\PhpNats\Exception\NatsConnectionRefusedException;
use EJTJ3\PhpNats\Exception\NatsInvalidResponseException;
use EJTJ3\PhpNats\Exception\TransportAlreadyConnectedException;
use EJTJ3\PhpNats\Logger\NullLogger;
use EJTJ3\PhpNats\ServerInfo\ServerInfo;
use EJTJ3\PhpNats\Transport\NatsTransportInterface;
use EJTJ3\PhpNats\Transport\Stream\StreamTransport;
use EJTJ3\PhpNats\Transport\TranssportOption;
use EJTJ3\PhpNats\Util\StringUtil;
use Exception;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * @author Evert Jan Hakvoort <evertjan@hakvoort.io>
 */
final class NatsConnection implements LoggerAwareInterface
{
    private EncoderInterface $encoder;

    private NatsTransportInterface $transport;

    private NatsConnectionOptionInterface $connectionOptions;

    private ?ServerInfo $serverInfo;

    private ?Server $currentServer;

    private LoggerInterface $logger;

    private bool $connected;

    public function __construct(
        NatsConnectionOptionInterface $connectionOptions,
        NatsTransportInterface $transport = new StreamTransport(),
        EncoderInterface $encoder = new JsonEncoder(),
        LoggerInterface $logger = new NullLogger()
    ) {
        $this->transport = $transport;
        $this->encoder = $encoder;
        $this->connectionOptions = $connectionOptions;
        $this->serverInfo = null;
        $this->logger = $logger;
        $this->currentServer = null;
        $this->connected = false;
    }

    /**
     * @throws Exception
     */
    public function connect(): void
    {
        if ($this->transport->isConnected()) {
            throw new TransportAlreadyConnectedException('Transport is already connected');
        }

        foreach ($this->connectionOptions->getServerCollection()->getServers() as $server) {
            $transportOption = new TranssportOption(
                $server->getHost(),
                $server->getPort(),
                $this->connectionOptions->getTimeout()
            );

            try {
                $this->transport->connect($transportOption);
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());

                continue;
            }

            $this->currentServer = $server;

            break;
        }

        if ($this->currentServer === null) {
            throw new NatsConnectionRefusedException('Could not connect to servers!');
        }

        $this->logger->debug(sprintf('Connected to %s', $this->currentServer->getHost()));

        // Create server info
        $serverInfo = $this->createServerInfo();

        // Check TLS
        if ($serverInfo->isTlsRequired() || $this->currentServer->isTls()) {
            $this->transport->enableTls();
        }

        $this->doConnect();

        // Validate PING response
        $this->validatePing();

        $this->connected = true;
    }

    public function isConnected(): bool
    {
        return $this->connected;
    }

    public function close(): void
    {
        $this->transport->close();
    }

    /**
     * @throws Exception
     */
    public function publish(string $subject, string $payload): void
    {
        if ($this->isConnected() === false) {
            throw new NatsConnectionRefusedException('Connection is closed');
        }

        $message = sprintf('%s %s', $subject, strlen($payload));

        $this->doWrite(NatsProtocolOperation::Pub, $message . Nats::CR_LF . $payload);
    }

    private function isErrorResponse(string $response): bool
    {
        return NatsProtocolOperation::Err->isOperation(substr($response, 0, 4));
    }

    /**
     * @throws Exception
     */
    public function ping(): void
    {
        $this->doWrite(NatsProtocolOperation::Ping, "ping");
    }

    /**
     * @throws Exception
     */
    public function validatePing(): void
    {
        $this->ping();

        $pingResponse = $this->getResponse();

        if (!NatsProtocolOperation::Pong->isOperation($pingResponse)) {
            throw new NatsInvalidResponseException('Did not receive a pong from the server');
        }
    }

    public function getServerInfo(): ?ServerInfo
    {
        return $this->serverInfo;
    }

    /**
     * @throws Exception
     */
    private function doConnect(): void
    {
        if ($this->currentServer === null) {
            throw new NatsConnectionRefusedException('No current server is connected');
        }

        $server = $this->currentServer;

        $connectionOptions = new ClientConnectionOptions();
        $connectionOptions->setPedantic(true);
        $connectionOptions->setVerbose(true);

        if (!StringUtil::isEmpty($server->getUser()) && !StringUtil::isEmpty($server->getPassword())) {
            $connectionOptions->setUser($server->getUser());
            $connectionOptions->setPassword($server->getPassword());
        }

        $this->doWrite(NatsProtocolOperation::Connect, $connectionOptions->toArray());

        if ($connectionOptions->isVerbose() === true) {
            $connectResponse = $this->getResponse();

            if (NatsProtocolOperation::Ack->isOperation($connectResponse) === false) {
                throw new NatsInvalidResponseException('Nats did not send a normal response');
            }
        }
    }

    private function createServerInfo(): ServerInfo
    {
        $rawData = $this->getResponse();

        [$operation, $data] = explode(' ', $rawData);

        if (!NatsProtocolOperation::Info->isOperation($operation)) {
            throw new NatsInvalidResponseException('Server information is not correct');
        }

        $data = $this->encoder->decode($data);

        $serverInfo = ServerInfo::fromData($data);

        $this->serverInfo = $serverInfo;

        return $serverInfo;
    }

    private function getResponse(): string
    {
        $response = $this->transport->receive();

        if ($response === false) {
            throw new NatsInvalidResponseException('Did not get any response from nats. Connection is not valid');
        }

        if (StringUtil::isEmpty($response)) {
            throw new NatsInvalidResponseException('Got an empty response from nats, try using tls instead');
        }

        if ($this->isErrorResponse($response)) {
            throw new NatsInvalidResponseException(sprintf('Receive an error response from nats: %s', $response));
        }

        return trim($response);
    }

    /**
     * @param array<string, mixed>|string|null $payload
     *
     * @throws Exception
     */
    private function doWrite(NatsProtocolOperation $operation, array|string $payload): void
    {
        if (!is_string($payload)) {
            $payload = $this->encoder->encode($payload);
        }

        $payload = sprintf('%s %s%s', $operation->value, $payload, Nats::CR_LF);

        $this->transport->write($payload);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
