<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use EJTJ3\PhpNats\Constant\Nats;
use EJTJ3\PhpNats\Constant\NatsProtocolOperation;
use EJTJ3\PhpNats\Encoder\EncoderInterface;
use EJTJ3\PhpNats\Encoder\JsonEncoder;
use EJTJ3\PhpNats\Exception\NatsConnectionRefusedException;
use EJTJ3\PhpNats\Exception\NatsInvalidResponseException;
use EJTJ3\PhpNats\Logger\NullLogger;
use EJTJ3\PhpNats\Transport\NatsTransportInterface;
use EJTJ3\PhpNats\Transport\Stream\StreamTransport;
use EJTJ3\PhpNats\Transport\TranssportOption;
use EJTJ3\PhpNats\Util\StringUtil;
use Exception;
use InvalidArgumentException;
use LogicException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

final class NatsConnection implements LoggerAwareInterface
{
    private ?ServerInfo $serverInfo;

    private ?Server $currentServer;

    private bool $connected;

    private bool $enableNoResponder;

    /**
     * @deprecated
     */
    private bool $isVerbose;

    public function __construct(
        private readonly NatsConnectionOptionInterface $connectionOptions,
        private readonly NatsTransportInterface $transport = new StreamTransport(),
        private readonly EncoderInterface $encoder = new JsonEncoder(),
        private LoggerInterface $logger = new NullLogger(),
    ) {
        $this->serverInfo = null;
        $this->currentServer = null;
        $this->connected = false;
        $this->isVerbose = false;
        $this->enableNoResponder = false;
    }

    public function setNoResponders(bool $enabled = true): void
    {
        if ($this->isConnected()) {
            throw new InvalidArgumentException('Stream is already connected, enable no-responders before connecting. ');
        }

        // Headers must be enabled for no responders.
        $this->enableNoResponder = $enabled;
    }

    public function setVerbose(bool $verbose): void
    {
        if ($this->isConnected()) {
            throw new InvalidArgumentException('Stream is already connected, enable verbose before connecting. ');
        }

        $this->isVerbose = $verbose;
    }

    /**
     * @throws Exception
     */
    public function connect(): void
    {
        if ($this->transport->isConnected()) {
            return;
        }

        foreach ($this->connectionOptions->getServerCollection()->getServers() as $server) {
            $transportOption = new TranssportOption(
                host: $server->getHost(),
                port: $server->getPort(),
                timeout: $this->connectionOptions->getTimeout()
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
            throw new NatsConnectionRefusedException('nats: no servers available for connection');
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

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * The PUB message publishes the message payload to the given subject name,
     * optionally supplying a reply subject. If a reply subject is supplied, it will be delivered to eligible
     * subscribers along with the supplied payload. Note that the payload itself is optional.
     * To omit the payload, set the payload size to 0, but the second CRLF is still required.
     *
     * @param string $subject the destination subject to publish to
     * @param string $payload the message payload data
     * @param string|null $replyTo the reply subject that subscribers can use to send a response back
     *  to the publisher/requester
     *
     * @throws Exception
     */
    public function publish(string $subject, string $payload, ?string $replyTo = null): void
    {
        if ($this->isConnected() === false) {
            throw new NatsConnectionRefusedException('Connection is closed');
        }

        $content = [$subject];

        if (!StringUtil::isEmpty($replyTo)) {
            $content[] = $replyTo;
        }

        $content[] = strlen($payload) . Nats::CR_LF . $payload;

        $this->doWrite(NatsProtocolOperation::Pub, implode(' ', $content));
        $this->validateAcknowledgement();
    }

    /**
     * @throws Exception
     */
    private function ping(): void
    {
        $this->doWrite(NatsProtocolOperation::Ping, 'ping');
    }

    /**
     * @throws Exception
     */
    public function validatePing(): void
    {
        $this->ping();

        $msg = $this->getMsg();

        if ($msg instanceof Pong) {
            return;
        }

        throw new NatsInvalidResponseException('Did not receive a pong from the server');
    }

    /**
     * @throws Exception
     */
    public function request(string $subject, string $payload = '', ?string $reply = null): MessageInterface
    {
        $replySubject = StringUtil::isEmpty($reply) ? self::createSid() : $reply;

        $sub = $this->subscribe($replySubject, null);

        $this->publish($subject, $payload, $replySubject);

        // process msg
        $msg = $this->getMsg();

        $this->unsubscribe($sub->subscriptionId);

        if (!$msg instanceof MessageInterface) {
            throw new InvalidArgumentException('Invalid response from nats');
        }

        if ($msg instanceof HMsg) {
            if ($msg->getHeader('status') === Nats::HEADER_NO_RESPONDER) {
                throw new InvalidArgumentException('No responders are available');
            }
        }

        return $msg;
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

        if ($this->enableNoResponder) {
            $connectionOptions->setNoResponders(true);
            $connectionOptions->setHeaders(true);
        }

        if ($this->isVerbose === true) {
            $connectionOptions->setVerbose(true);
        }

        if (!StringUtil::isEmpty($server->getUser()) && !StringUtil::isEmpty($server->getPassword())) {
            $connectionOptions->setUser($server->getUser());
            $connectionOptions->setPassword($server->getPassword());
        }

        $this->doWrite(NatsProtocolOperation::Connect, $connectionOptions->toArray());
        $this->validateAcknowledgement();
    }

    /**
     * initiates a subscription to a subject, optionally joining a distributed queue group.
     *
     * @param string|null $queueGroup if specified, the subscriber will join this queue group
     * @param string $subject the subject name to subscribe to
     *
     * @throws Exception
     */
    private function subscribe(string $subject, ?string $queueGroup): Subscription
    {
        $subscriptionId = self::createSid();
        $sub = new Subscription($subject, $subscriptionId);

        $payload = [$subject];

        if (!StringUtil::isEmpty($queueGroup)) {
            $payload[] = $queueGroup;
        }

        $payload[] = $subscriptionId;
        $this->doWrite(NatsProtocolOperation::Sub, implode(' ', $payload));
        $this->validateAcknowledgement();

        return $sub;
    }

    /**
     * @throws Exception
     */
    private function unsubscribe(string $subscriptionId): void
    {
        $this->doWrite(NatsProtocolOperation::Unsub, $subscriptionId);
        $this->validateAcknowledgement();
    }

    /**
     * @throws Exception
     */
    private function saveRead(int $maxBytes = 0, int $timeout = 100): string
    {
        $line = '';
        $timeoutTarget = microtime(true) + $timeout;
        $receivedBytes = 0;
        while ($receivedBytes < $maxBytes || $maxBytes === 0) {
            $chunkSize = 1024;
            $bytesLeft = ($maxBytes - $receivedBytes);

            if ($maxBytes !== 0 && $bytesLeft < $chunkSize) {
                $chunkSize = $bytesLeft;
            }

            $read = $this->transport->read($chunkSize, Nats::CR_LF);

            if ($read === false) {
                throw new Exception('Could not read from stream');
            }

            if (is_string($read)) {
                $len = strlen($read);

                $receivedBytes += $len;

                $line .= $read;

                // End of string is reached
                if ($len < 1024) {
                    break;
                }
            }

            if (microtime(true) >= $timeoutTarget) {
                throw new InvalidArgumentException('Timeout reached');
            }
        }

        return $line;
    }

    /**
     * @throws Exception
     */
    private function validateAcknowledgement(): void
    {
        if ($this->isVerbose === false) {
            return;
        }

        $ack = $this->getMsg();

        if (!$ack instanceof Acknowledgement) {
            throw new NatsInvalidResponseException('Nats did not send a ack. response');
        }
    }

    private function createServerInfo(): ServerInfo
    {
        $serverInfoMsg = $this->getMsg();

        if (!$serverInfoMsg instanceof ServerInfo) {
            throw new InvalidArgumentException('Invalid Response');
        }

        $this->serverInfo = $serverInfoMsg;

        return $serverInfoMsg;
    }

    /**
     * @throws Exception
     */
    private function getMsg(): NatsResponseInterface
    {
        $line = $this->saveRead(0, 300);

        $response = Response::parse($line);

        if ($response instanceof Ping) {
            $this->pong();

            // @TODO add check for infinite loop
            return $this->getMsg();
        }

        if ($response instanceof ServerInfo || $response instanceof Pong || $response instanceof Acknowledgement || $response instanceof Error) {
            return $response;
        }

        if ($response instanceof Msg) {
            $payload = $this->saveRead($response->bytes);
            $response->setPayload($payload);

            return $response;
        }

        if ($response instanceof HMsg) {
            $headers = $this->saveRead($response->headerBytes);
            $response->setHeaders($headers);
            $payload = $this->saveRead($response->totalBytes - $response->headerBytes);
            $response->setPayload($payload);

            return $response;
        }

        throw new LogicException('Msg type is not yet implemented');
    }

    /**
     * @param array<string, mixed>|string $payload
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

    /**
     * @throws Exception
     */
    private function pong(): void
    {
        $this->doWrite(NatsProtocolOperation::Pong, 'pong');
    }

    /** A unique alphanumeric subscription ID, generated by the client. */
    private static function createSid(): string
    {
        return bin2hex(random_bytes(4));
    }
}
