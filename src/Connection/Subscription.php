<?php

declare(strict_types=1);

namespace EJTJ3\PhpNats\Connection;

final class Subscription
{
    public function __construct(
        public readonly string $subject,
        public readonly string $subscriptionId,
    ) {
    }
}
