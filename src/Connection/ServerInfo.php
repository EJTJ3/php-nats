<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

/**
 * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#info
 */
final class ServerInfo implements NatsResponseInterface
{
    public function __construct(
        /**
         * The unique identifier of the NATS server.
         */
        private readonly string $serverId,

        /**
         * The version of the NATS server.
         */
        private readonly string $version,

        /**
         * The version of golang the NATS server was built with.
         */
        private readonly ?string $go,

        /**
         * The IP address used to start the NATS server,
         * by default this will be 0.0.0.0 and can be configured with -client_advertise host:port.
         */
        private readonly string $host,

        /**
         * The port number the NATS server is configured to listen on.
         */
        private readonly int $port,

        /**
         * An integer indicating the protocol version of the server.
         * The server version 1.2.0 sets this to 1 to indicate that it supports the "Echo" feature.
         */
        private readonly int $proto,

        /**
         * Maximum payload size, in bytes, that the server will accept from the client.
         */
        private readonly int $maxPayload,

        /**
         * If this is set, then the client must perform the TLS/1.2 handshake.
         * Note, this used to be ssl_required and has been updated along with the protocol from SSL to TLS.
         */
        private readonly bool $tlsRequired,

        /**
         *  If this is set, the client must provide a valid certificate during the TLS handshake.
         */
        private readonly bool $tlsVerify,

        /**
         * If this is set, then the client should try to authenticate upon connect.
         */
        private readonly bool $authRequired,

        /**
         * @var string[] $connectUrls
         *
         * An optional list of server urls that a client can connect to
         */
        private readonly array $connectUrls,

        /**
         * An optional unsigned integer (64 bits) representing the internal client identifier in the server.
         * This can be used to filter client connections in monitoring, correlate with error logs, etc...
         */
        private readonly ?int $clientId,

        /**
         * If the server supports Lame Duck Mode notifications,
         * and the current server has transitioned to lame duck, ldm will be set to true.
         */
        private readonly bool $lameDuckMode,
    ) {
    }

    public static function fromData(string $response): self
    {
        /** @var array{
         *      server_id: string,
         *     version: string,
         *     go: string|null,
         *     host: string,
         *     port: int,
         *     proto: int,
         *     max_payload: int,
         *     tls_required: bool|null,
         *     tls_verify: bool|null,
         *     auth_required: bool|null,
         *     connect_urls: array<string>|null,
         *     client_id: int|null,
         *     ldm: boolean|null,
         * } $content
         */
        $content = json_decode($response, true);

        return new self(
            serverId: $content['server_id'],
            version: $content['version'],
            go: $content['go'] ?? null,
            host: $content['host'],
            port: $content['port'],
            proto: $content['proto'],
            maxPayload: $content['max_payload'],
            tlsRequired: $content['tls_required'] ?? false,
            tlsVerify: $content['tls_verify'] ?? false,
            authRequired: $content['auth_required'] ?? false,
            connectUrls: $content['connect_urls'] ?? [],
            clientId: $content['client_id'] ?? null,
            lameDuckMode: $content['ldm'] ?? false
        );
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
