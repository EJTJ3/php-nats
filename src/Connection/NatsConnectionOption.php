<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

use EJTJ3\PhpNats\Util\StringUtil;
use InvalidArgumentException;

final class NatsConnectionOption implements NatsConnectionOptionInterface
{
    /**
     * A list of server URLs where the client should attempt a connection.
     */
    private ServerCollection $serverCollection;

    /**
     * A name for the client. Useful for identifying a client on the server monitoring and logs.
     */
    private ?string $name;

    /**
     * Connection timeout.
     */
    private int $timeout;

    /**
     * @param array<int, string|Server>|Server|string|ServerCollection $servers
     */
    public function __construct(
        array|Server|string|ServerCollection $servers = [],
        ?string $name = null,
        int $timeout = 5,
        bool $randomize = false,
    ) {
        if (is_string($servers) && StringUtil::isEmpty($servers) === false) {
            $servers = explode(',', $servers);
        }

        if ($servers instanceof ServerCollection) {
            $this->serverCollection = $servers;
        } else {
            if ($servers instanceof Server) {
                $serverCollection = [$servers];
            } elseif (is_array($servers)) {
                $serverCollection = array_map(static function ($server): Server {
                    if ($server instanceof Server) {
                        return $server;
                    }

                    if (is_string($server) && StringUtil::isEmpty($server) === false) {
                        return new Server($server);
                    }

                    throw new InvalidArgumentException('Server must be of type string');
                }, $servers);
            } else {
                throw new InvalidArgumentException('Server must be of type string');
            }

            $this->serverCollection = new ServerCollection($serverCollection, $randomize);
        }

        $this->name = $name;
        $this->timeout = $timeout;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getServerCollection(): ServerCollection
    {
        return $this->serverCollection;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
