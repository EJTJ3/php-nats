<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\Error;
use EJTJ3\PhpNats\Connection\NatsResponseInterface;
use PHPUnit\Framework\TestCase;

final class ErrorTest extends TestCase
{
    public function testErrorWithMessage(): void
    {
        $error = new Error('Unknown Protocol Operation');

        $this->assertSame('Unknown Protocol Operation', $error->getMessage());
        $this->assertInstanceOf(NatsResponseInterface::class, $error);
    }

    public function testErrorWithoutMessage(): void
    {
        $error = new Error();

        $this->assertSame('', $error->getMessage());
    }
}
