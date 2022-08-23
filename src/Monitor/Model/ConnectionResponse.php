<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Monitor\Model;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

final class ConnectionResponse
{
    /**
     * @Serializer\SerializedName("server_id")
     */
    public string $serverId;

    /**
     * @Serializer\Type("NatsDateTimeImmutable")
     */
    public DateTimeImmutable $now;

    /**
     * @Serializer\SerializedName("num_connections")
     */
    public int $numberOfConnections;

    public int $total;

    public int $offset;

    public int $limit;

    /**
     * @var Connection[]
     *
     * @Serializer\Type("array<EJTJ3\PhpNats\Monitor\Model\Connection>")
     */
    public array $connections;

    public function __construct(
        string $serverId,
        DateTimeImmutable $now,
        int $numberOfConnections,
        int $total,
        int $offset,
        int $limit,
        array $connections
    ) {
        $this->serverId = $serverId;
        $this->now = $now;
        $this->numberOfConnections = $numberOfConnections;
        $this->total = $total;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->connections = $connections;
    }
}
