<?php

declare(strict_types=1);

use EJTJ3\PhpNats\Connection\NatsConnection;
use EJTJ3\PhpNats\Connection\NatsConnectionOption;

require __DIR__ . '/../vendor/autoload.php';

// Connect to a cluster with multiple servers
// The client will try each server in order until one succeeds
$option = new NatsConnectionOption(
    servers: [
        'nats://admin:admin@nats-server0.example.com:4222',
        'nats://admin:admin@nats-server1.example.com:4222',
        'nats://admin:admin@nats-server2.example.com:4222',
    ],
    name: 'cluster example',
    timeout: 5,
    randomize: true, // randomize server order for load balancing
);

$connection = new NatsConnection($option);

$connection->connect();

if ($connection->isConnected()) {
    $connection->publish('hello', 'world');
}

$connection->close();
