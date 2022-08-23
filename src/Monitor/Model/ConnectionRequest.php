<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Monitor\Model;

use InvalidArgumentException;

/**
 * @see https://docs.nats.io/running-a-nats-service/nats_admin/monitoring#connection-information
 */
final class ConnectionRequest
{
    public const STATE_OPEN = 'open';

    public const STATE_CLOSED = 'closed';

    public const STATE_ANY = 'any';

    /**
     * Sort by connection ID
     */
    public const SORT_CID = 'cid';

    /**
     * Sort by connection start time, same as CID
     */
    public const SORT_START = 'start';

    /**
     * Sort by number of subscriptions
     */
    public const SORT_SUBS = 'subs';

    /**
     * Sort by amount of data in bytes waiting to be sent to client
     */
    public const SORT_PENDING = 'pending';

    /**
     * Sort by: number of messages sent
     */
    public const SORT_MSG_SENT = 'msgs_to';

    public const SORT_MSG_REC = 'msgs_from';

    /**
     * Sort by: number of bytes sent
     */
    public const SORT_BYTES_SENT = 'bytes_to';

    public const SORT_BYTES_REC = 'bytes_from';

    /**
     * Sort by Lifetime of the connection
     */
    public const SORT_UPTIME = 'uptime';

    /**
     * Sort by: Stop time for a closed connection
     */
    public const SORT_STOP_TIME = 'stop';
    /**
     * Sort by: reason for a closed connection
     */
    public const SORT_REASON = 'reason';

    public const SORT_OPTIONS = [
        self::SORT_BYTES_REC,
        self::SORT_BYTES_SENT,
        self::SORT_CID,
        self::SORT_MSG_REC,
        self::SORT_START,
        self::SORT_STOP_TIME,
        self::SORT_UPTIME,
    ];

    public const subs = 'subs';

    public const pending = 'pending';

    /**
     * Include username in response
     */
    private bool $auth;

    /**
     * Include subscriptions.
     * When set to detail a list with more detailed subscription information will be returned.
     */
    private bool $subs;

    /**
     * Pagination offset. Default is 0.
     */
    private ?int $offset;

    /**
     * @var int|null Number of results to return. Default is 1024.
     */
    private ?int $limit;


    /**
     * Return a connection by it's id
     */
    private ?int $cid;

    /**
     * Return connections of particular state. Default is open.
     */
    private ?string $state;

    /**
     * Filter the connection with this MQTT client ID.
     */
    private ?string $mqttClient;

    private ?string $sort;

    public function __construct()
    {
        $this->auth = false;
        $this->state = self::STATE_OPEN;
        $this->subs = true;
        $this->offset = null;
        $this->limit = null;
        $this->cid = null;
        $this->mqttClient = null;
        $this->sort = null;
    }

    public function isSubs(): bool
    {
        return $this->subs;
    }

    public function setSubs(bool $subs): void
    {
        $this->subs = $subs;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    public function getCid(): ?int
    {
        return $this->cid;
    }

    public function setCid(?int $cid): void
    {
        $this->cid = $cid;
    }

    public function getMqttClient(): ?string
    {
        return $this->mqttClient;
    }

    public function setMqttClient(?string $mqttClient): void
    {
        $this->mqttClient = $mqttClient;
    }

    public function getSort(): ?string
    {
        return $this->sort;
    }

    public function setSort(?string $sort): void
    {
        if (in_array($sort, self::SORT_OPTIONS, true)) {
            throw new InvalidArgumentException('Sort option does not exist');
        }

        $this->sort = $sort;
    }

    public function setAuth(bool $auth): void
    {
        $this->auth = $auth;
    }


    public function setState(string $state): void
    {
        if (!in_array($state, [self::STATE_OPEN, self::STATE_CLOSED, self::STATE_ANY])) {
            throw new InvalidArgumentException('State does not exist');
        }

        $this->state = $state;
    }

    public function toArray(): array
    {
        return [
            'auth' => $this->auth,
            'subs' => $this->subs,
            'state' => $this->state,
            'offset' => $this->offset,
            'limit' => $this->limit,
            'cid' => $this->cid,
            'mqtt_client' => $this->mqttClient,
            'sort' => $this->sort,
        ];
    }
}
