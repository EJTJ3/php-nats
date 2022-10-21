<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use InvalidArgumentException;

final class ServerCollection
{
    /**
     * @var Server[]
     */
    private array $servers;

    /**
     * @param Server[] $servers
     */
    public function __construct(array $servers, bool $randomize = false)
    {
        $this->servers = [];

        if (count($servers) === 0) {
            throw new InvalidArgumentException('Servers are empty.');
        }

        if ($randomize === true) {
            if (shuffle($servers) === false) {
                throw new InvalidArgumentException('Servers could not be randomised.');
            }
        }

        foreach ($servers as $server) {
            if (!$server instanceof Server) {
                throw new InvalidArgumentException(sprintf('Servers must be of type %s.', Server::class));
            }

            $this->addServer($server);
        }
    }

    public function addServer(Server $server): void
    {
        $this->servers[] = $server;
    }

    /**
     * @return Server[]
     */
    public function getServers(): array
    {
        return $this->servers;
    }
}
