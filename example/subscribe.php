<?php

declare(strict_types=1);

use EJTJ3\PhpNats\Connection\MessageInterface;
use EJTJ3\PhpNats\Connection\NatsConnection;
use EJTJ3\PhpNats\Connection\NatsConnectionOption;

require __DIR__ . '/../vendor/autoload.php';

$dsn = 'nats://admin:admin@0.0.0.0:4222';

$option = new NatsConnectionOption($dsn, 'subscribe example', 5);
$connection = new NatsConnection($option);

$connection->connect();

if ($connection->isConnected()) {
    $subscription = $connection->subscribe('my.subject');

    echo 'Waiting for messages on "my.subject"...' . PHP_EOL;

    // Wait for a message
    $msg = $connection->getMsg();

    if ($msg instanceof MessageInterface) {
        echo 'Received: ' . $msg->getPayload() . PHP_EOL;
    }

    $connection->unsubscribe($subscription->subscriptionId);
}

$connection->close();
