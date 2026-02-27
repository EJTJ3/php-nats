<?php

declare(strict_types=1);

use EJTJ3\PhpNats\Connection\NatsConnection;
use EJTJ3\PhpNats\Connection\NatsConnectionOption;

require __DIR__ . '/../vendor/autoload.php';

$dsn = 'nats://admin:admin@0.0.0.0:4222';

$option = new NatsConnectionOption($dsn, 'request example', 5);
$connection = new NatsConnection($option);

$connection->connect();

if ($connection->isConnected()) {
    $response = $connection->request('my.subject', 'request payload');

    echo 'Response: ' . $response->getPayload() . PHP_EOL;
}

$connection->close();
