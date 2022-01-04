# Nats publisher

This is a simple package to publish messages to [Nats](https://github.com/nats-io/nats-server)

## Installation

You can install the package using the [Composer](https://getcomposer.org/) package manager. You can install it by running this command in your project root:

```sh
composer require ejtj3/php-nats
```

## Setup a nats connection

```php
<?php

declare(strict_types=1);

use EJTJ3\PhpNats\Connection\NatsConnectionOption;
use EJTJ3\PhpNats\Connection\NatsConnection;

$connectionOptions = new NatsConnectionOption('nats://nats-server.com:4222');
$connection = new NatsConnection($connectionOptions);

// connect to nats-server
$connection->connect();
// send ping and wait for the 'PONG' response
$connection->validatePing();
// close connection
$connection->close();

```


## Setup a nats connection with auth

```php
<?php

declare(strict_types=1);

use EJTJ3\PhpNats\Connection\NatsConnectionOption;

$connectionOptions = new NatsConnectionOption('nats://admin:admin@nats-server.com:4222');

```

## Setup a nats connection with TLS

```php
<?php

declare(strict_types=1);

use EJTJ3\PhpNats\Connection\NatsConnectionOption;

$connectionOptions = new NatsConnectionOption('tls://admin:admin@nats-server.com:4222');

```

## Publish message

```php
<?php

declare(strict_types=1);

// connect to nats-server
$connection->connect();

$subject = 'hello';
$payload = 'world';

// publish world to the hello subject
$connection->publish($subject, $payload);

// close connection
$connection->close();

```

## Connect to a cluster

```php
<?php

declare(strict_types=1);

use EJTJ3\PhpNats\Connection\NatsConnectionOption;
use EJTJ3\PhpNats\Connection\NatsConnection;

$connectionOptions = new NatsConnectionOption('nats://admin:admin@nats-server0.com:4222,nats://admin:admin@nats-server1.com:4222,nats://admin:admin@nats-server2.com:4222');

____________________OR________________________

$connectionOptions = new NatsConnectionOption([
    'nats://admin:admin@nats-server0.com:4222',
    'nats://admin:admin@nats-server1.com:4222',
    'nats://admin:admin@nats-server2.com:4222',
]);

$connection = new NatsConnection($connectionOptions);
```
