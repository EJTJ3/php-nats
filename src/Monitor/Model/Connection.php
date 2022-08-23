<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Monitor\Model;

use DateInterval;
use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;

final class Connection
{
    public int $cid;

    public string $kind;

    public string $type;

    public string $ip;

    public int $port;

    /**
     * @Serializer\SerializedName("last_activity")
     * @Serializer\Type("NatsDateTimeImmutable")
     */
    public DateTimeImmutable $start;

    /**
     * @Serializer\SerializedName("last_activity")
     * @Serializer\Type("NatsDateTimeImmutable")
     *
     */
    public ?DateTimeImmutable $stop = null;

    /**
     * @Serializer\SerializedName("last_activity")
     * @Serializer\Type("NatsDateTimeImmutable")
     *
     */
    public DateTimeImmutable $lastActivity;

    public string $rtt;

    /**
     * @Serializer\Type("NatsDateInterval")
     */
    public DateInterval $uptime;

    /**
     * @Serializer\Type("NatsDateInterval")
     */
    public DateInterval $idle;

    /**
     * @Serializer\SerializedName("last_activity")
     */
    public int $pending_bytes;

    /**
     * @Serializer\SerializedName("in_msgs")
     */
    public int $messagesIn;

    /**
     * @Serializer\SerializedName("out_msgs")
     */
    public int $messagesOut;

    /**
     * @Serializer\SerializedName("in_bYtes")
     */
    public int $bytesIn = 0;

    /**
     * @Serializer\SerializedName("out_bytes")
     */
    public int $bytesOut = 0;

    /**
     * @Serializer\SerializedName("subscriptions")
     */
    public int $subscriptionCount = 0;

    public ?string $name = null;

    public ?string $lang = null;

    public ?string $version = null;

    public ?string $tls_version = null;

    /**
     * @Serializer\SerializedName("tls_cipher_suite")
     */
    public ?string $tlsCipherSuite = null;

    /**
     * @Serializer\Type("array<int, string>")
     * @Serializer\SerializedName("subscriptions_list")
     */
    public ?array $subscriptionsList = [];

    public ?string $reason = null;

    public function __construct(
        int                $cid,
        string             $kind,
        string             $type,
        string             $ip,
        int                $port,
        DateTimeImmutable  $start,
        ?DateTimeImmutable $stop,
        DateTimeImmutable  $last_activity,
        string             $rtt,
        DateInterval       $uptime,
        DateInterval       $idle,
        int                $pending_bytes,
        int                $messagesIn,
        int                $messagesOut,
        int                $bytesIn,
        int                $bytesOut,
        int                $subscriptionCount,
        string             $name,
        string             $lang,
        string             $version,
        ?string            $reason,
        ?array             $subscriptionsList = [],
        ?string            $tlsCipherSuite = null
    )
    {
        $this->tls_version = null;
        $this->cid = $cid;
        $this->kind = $kind;
        $this->type = $type;
        $this->ip = $ip;
        $this->port = $port;
        $this->start = $start;
        $this->stop = $stop;
        $this->lastActivity = $last_activity;
        $this->rtt = $rtt;
        $this->uptime = $uptime;
        $this->idle = $idle;
        $this->pending_bytes = $pending_bytes;
        $this->messagesIn = $messagesIn;
        $this->messagesOut = $messagesOut;
        $this->bytesIn = $bytesIn;
        $this->bytesOut = $bytesOut;
        $this->subscriptionsList = $subscriptionsList;
        $this->subscriptionCount = $subscriptionCount;
        $this->name = $name;
        $this->lang = $lang;
        $this->version = $version;
        $this->reason = $reason;
        $this->tlsCipherSuite = $tlsCipherSuite;
    }
}
