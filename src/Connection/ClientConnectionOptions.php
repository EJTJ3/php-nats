<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use EJTJ3\PhpNats\Constant\Nats;

/**
 * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#connect
 */
final class ClientConnectionOptions
{
    public function __construct(
        /**
         * Turns on +OK protocol acknowledgements.
         */
        private bool $verbose = false,

        /**
         * Turns on additional strict format checking, e.g. for properly formed subjects.
         */
        private bool $pedantic = true,

        /**
         * Indicates whether the client requires an SSL connection.
         */
        private bool $tlsRequired = false,

        /**
         * Client authorization token (if auth_required is set).
         */
        private ?string $authToken = null,

        /**
         * Connection username (if auth_required is set).
         */
        private ?string $user = null,

        /**
         * Connection password (if auth_required is set).
         */
        private ?string $password = null,

        /**
         * Optional client name.
         */
        private ?string $name = null,

        /**
         * Sending 0 (or absent) indicates client supports original protocol.
         * Sending 1 indicates that the client supports dynamic reconfiguration of
         * cluster topology changes by asynchronously receiving INFO messages with known servers it can reconnect to.
         */
        private int $protocol = 0,

        /**
         * If set to true, the server (version 1.2.0+) will not send originating messages from this
         * connection to its own subscriptions. Clients should set this to true only for server supporting
         * this feature, which is when proto in the INFO protocol is set to at least 1.
         */
        private bool $echo = false,

        /**
         * Enable quick replies for cases where a request is sent to a topic with no responders.
         */
        private bool $noResponders = false,

        /**
         * Whether the client supports headers.
         */
        private bool $headers = false,
    ) {
    }

    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    public function setVerbose(bool $verbose): void
    {
        $this->verbose = $verbose;
    }

    public function isPedantic(): bool
    {
        return $this->pedantic;
    }

    public function setPedantic(bool $pedantic): void
    {
        $this->pedantic = $pedantic;
    }

    public function isTlsRequired(): bool
    {
        return $this->tlsRequired;
    }

    public function setTlsRequired(bool $tlsRequired): void
    {
        $this->tlsRequired = $tlsRequired;
    }

    public function getAuthToken(): ?string
    {
        return $this->authToken;
    }

    public function setAuthToken(?string $authToken): void
    {
        $this->authToken = $authToken;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): void
    {
        $this->user = $user;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getProtocol(): int
    {
        return $this->protocol;
    }

    public function setProtocol(int $protocol): void
    {
        $this->protocol = $protocol;
    }

    public function isEcho(): bool
    {
        return $this->echo;
    }

    public function setEcho(bool $echo): void
    {
        $this->echo = $echo;
    }

    public function isNoResponders(): bool
    {
        return $this->noResponders;
    }

    public function setNoResponders(bool $noResponders): void
    {
        $this->noResponders = $noResponders;
    }

    public function isHeaders(): bool
    {
        return $this->headers;
    }

    public function setHeaders(bool $headers): void
    {
        $this->headers = $headers;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'verbose' => $this->verbose,
            'pedantic' => $this->pedantic,
            'tls_required' => $this->tlsRequired,
            'auth_token' => $this->authToken,
            'user' => $this->user,
            'pass' => $this->password,
            'name' => $this->name,
            'lang' => Nats::LANG,
            'version' => Nats::VERSION,
            'protocol' => $this->protocol,
            'echo' => $this->echo,
            'no_responders' => $this->noResponders,
            'headers' => $this->headers,
        ];
    }
}
