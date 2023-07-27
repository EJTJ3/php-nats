<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use EJTJ3\PhpNats\Constant\Nats;
use InvalidArgumentException;
use Nyholm\Dsn\Configuration\Url;
use Nyholm\Dsn\DsnParser;

final class Server
{
    private Url $url;

    public function __construct(string $url)
    {
        $url = $this->addDefaultScheme($url);

        $this->url = DsnParser::parseUrl($url);
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getUser(): ?string
    {
        return $this->url->getUser();
    }

    public function getPassword(): ?string
    {
        return $this->url->getPassword();
    }

    public function getPort(): int
    {
        return $this->url->getPort() ?? Nats::DEFAULT_PORT;
    }

    public function getHost(): string
    {
        return $this->url->getHost();
    }

    public function getScheme(): ?string
    {
        return $this->url->getScheme();
    }

    public function isTls(): bool
    {
        return $this->getScheme() === 'tls';
    }

    private function addDefaultScheme(string $url): string
    {
        $schemeParts = explode('://', $url);

        if (count($schemeParts) === 1) {
            $url = sprintf('nats://%s', $url);
        } elseif (!in_array($schemeParts[0], ['nats', 'tls'], true)) {
            throw new InvalidArgumentException('Scheme is not supported');
        }

        return $url;
    }
}
