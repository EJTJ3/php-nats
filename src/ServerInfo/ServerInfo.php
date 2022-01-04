<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\ServerInfo;

/**
 * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#info
 */
final class ServerInfo
{
    /**
     * The unique identifier of the NATS server.
     */
    private string $serverId;

    /**
     * The version of the NATS server.
     */
    private string $version;

    /**
     * The version of golang the NATS server was built with.
     */
    private ?string $go;

    /**
     * The IP address used to start the NATS server,
     * by default this will be 0.0.0.0 and can be configured with -client_advertise host:port.
     */
    private string $host;

    /**
     * The port number the NATS server is configured to listen on.
     */
    private int $port;

    /**
     * An integer indicating the protocol version of the server.
     * The server version 1.2.0 sets this to 1 to indicate that it supports the "Echo" feature.
     */
    private int $proto;

    /**
     * Maximum payload size, in bytes, that the server will accept from the client.
     */
    private int $maxPayload;

    /**
     * If this is set, then the client must perform the TLS/1.2 handshake.
     * Note, this used to be ssl_required and has been updated along with the protocol from SSL to TLS.
     */
    private bool $tlsRequired;

    /**
     *  If this is set, the client must provide a valid certificate during the TLS handshake.
     */
    private bool $tlsVerify;

    /**
     * If this is set, then the client should try to authenticate upon connect.
     */
    private bool $authRequired;

    /**
     * @var string[]
     *
     * An optional list of server urls that a client can connect to
     */
    private array $connectUrls;

    /**
     * An optional unsigned integer (64 bits) representing the internal client identifier in the server.
     * This can be used to filter client connections in monitoring, correlate with error logs, etc...
     */
    private ?int $clientId;

    /**
     * If the server supports Lame Duck Mode notifications,
     * and the current server has transitioned to lame duck, ldm will be set to true.
     */
    private bool $lameDuckMode;

    /**
     * @param array<string, string|bool|int> $data
     */
    public function __construct(array $data)
    {
        $this->serverId = $data['server_id'];
        $this->version = $data['version'];
        $this->go = $data['go'] ?? null;
        $this->host = $data['host'];
        $this->port = $data['port'];
        $this->proto = $data['proto'];
        $this->maxPayload = $data['max_payload'];
        $this->authRequired = $data['auth_required'] ?? false;
        $this->tlsRequired = $data['tls_required'] ?? false;
        $this->tlsVerify = $data['tls_verify'] ?? false;
        $this->connectUrls = $data['connect_urls'] ?? [];
        $this->clientId = $data['client_id'] ?? null;
        $this->lameDuckMode = $data['ldm'] ?? false;
    }

    /**
     * @return string[]
     */
    public function getConnectUrls(): array
    {
        return $this->connectUrls;
    }

    public function getMaxPayload(): int
    {
        return $this->maxPayload;
    }

    public function getClientId(): int
    {
        return $this->clientId;
    }

    public function getProto(): int
    {
        return $this->proto;
    }

    public function getServerId(): string
    {
        return $this->serverId;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function isTlsRequired(): ?bool
    {
        return $this->tlsRequired;
    }

    public function isTlsVerified(): ?bool
    {
        return $this->tlsVerify;
    }

    public function isLameDuckMode(): bool
    {
        return $this->lameDuckMode;
    }

    public function isAuthRequired(): bool
    {
        return $this->authRequired;
    }

    public function getGoVersion(): ?string
    {
        return $this->go;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }
}
