# CLAUDE.md

## Project Overview

`ejtj3/php-nats` is a PHP client library for publishing messages to a [NATS](https://nats.io) server. It supports plain TCP, TLS, authentication, and cluster connections.

- **Namespace:** `EJTJ3\PhpNats\`
- **PHP requirement:** ^8.3
- **Autoloading:** PSR-4, `src/` → `EJTJ3\PhpNats\`

## Directory Structure

```
src/
  Connection/   # NatsConnection, NatsConnectionOption, Server, Subscription, message types
  Constant/     # Protocol operation constants
  Encoder/      # EncoderInterface, JsonEncoder
  Exception/    # NatsExceptionInterface, NatsTimeoutException
  Logger/       # NullLogger (PSR-3 compatible)
  Transport/    # NatsTransportInterface, StreamTransport
  Util/         # StringUtil

tests/          # PHPUnit tests mirroring src/ structure
example/        # connect.php, publish.php usage examples
```

## Commands

### Install dependencies
```sh
composer install
```

### Run tests
```sh
composer test
# or directly:
vendor/bin/phpunit
```

### Static analysis
```sh
vendor/bin/phpstan analyse
```
PHPStan is configured at **level max** (`phpstan.neon`), analysing `src/` only.

## Code Conventions

- All files use `declare(strict_types=1)`
- Follow PSR-4 for file/class naming
- Use interfaces for public contracts (e.g. `NatsTransportInterface`, `EncoderInterface`)
- No framework dependencies — keep the library lean
- New source classes go in the appropriate `src/` subdirectory; matching tests go in `tests/`

## CI

GitHub Actions runs PHPUnit on every push (`.github/workflows/php-unit.yaml`). PRs should pass all tests and PHPStan before merging.
