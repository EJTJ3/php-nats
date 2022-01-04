<?php

declare(strict_types=1);

use EJTJ3\PhpNats\Connection\NatsConnection;
use EJTJ3\PhpNats\Connection\NatsConnectionOption;

require __DIR__ . '/../vendor/autoload.php';

$user = 'admin';
$password = 'admin';
$host = '0.0.0.0';
$port = '4222';

$dsn = "nats://$user:$password@$host:$port";

$option = new NatsConnectionOption($dsn, 'local test', 5, false);
$connection = new NatsConnection($option);

$connection->connect();

if ($connection->isConnected()) {
    var_dump($connection->getServerInfo());
}

$connection->close();
