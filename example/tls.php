<?php

declare(strict_types=1);

use EJTJ3\PhpNats\Connection\NatsConnection;
use EJTJ3\PhpNats\Connection\NatsConnectionOption;

require __DIR__ . '/../vendor/autoload.php';

// Use the tls:// scheme to enable TLS encryption
$dsn = 'tls://admin:admin@nats-server.example.com:4222';

$option = new NatsConnectionOption($dsn, 'tls example', 5);
$connection = new NatsConnection($option);

// TLS handshake will be performed automatically during connect
$connection->connect();

if ($connection->isConnected()) {
    $connection->publish('secure.subject', 'encrypted payload');
}

$connection->close();
