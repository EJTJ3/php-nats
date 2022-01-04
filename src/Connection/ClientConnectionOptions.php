<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use EJTJ3\PhpNats\Constant\Nats;

/**
 * @see https://docs.nats.io/reference/reference-protocols/nats-protocol#connect
 */
final class ClientConnectionOptions
{
    /**
     * Turns on +OK protocol acknowledgements.
     */
    private bool $verbose;

    /**
     * Turns on additional strict format checking, e.g. for properly formed subjects.
     */
    private bool $pedantic;

    /**
     * Indicates whether the client requires an SSL connection.
     */
    private bool $tlsRequired;

    /**
     * Client authorization token (if auth_required is set).
     */
    private ?string $authToken;

    /**
     * Connection username (if auth_required is set).
     */
    private ?string $user;

    /**
     * Connection password (if auth_required is set).
     */
    private ?string $password;

    /**
     * Optional client name.
     */
    private ?string $name;

    /**
     * Sending 0 (or absent) indicates client supports original protocol.
     * Sending 1 indicates that the client supports dynamic reconfiguration of
     * cluster topology changes by asynchronously receiving INFO messages with known servers it can reconnect to.
     */
    private ?int $protocol;

    /**
     * If set to true, the server (version 1.2.0+) will not send originating messages from this
     * connection to its own subscriptions. Clients should set this to true only for server supporting
     * this feature, which is when proto in the INFO protocol is set to at least 1.
     */
    private bool $echo;

    public function __construct()
    {
        $this->verbose = true;
        $this->pedantic = true;
        $this->tlsRequired = false;
        $this->authToken = null;
        $this->user = null;
        $this->password = null;
        $this->name = null;
        $this->protocol = 0;
        $this->echo = false;
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

    public function getProtocol(): ?int
    {
        return $this->protocol;
    }

    public function setProtocol(?int $protocol): void
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

    /**
     * @return  array<string, bool|int|string|null>
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
        ];
    }
}
