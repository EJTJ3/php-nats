<?php

declare(strict_types=1);

namespace Transport;

use EJTJ3\PhpNats\Transport\TranssportOption;
use PHPUnit\Framework\TestCase;

final class TransportOptionTest extends TestCase
{
    public function testTransportOption(): void
    {
        $option = new TranssportOption('host', 9222, 5);

        $this->assertSame(5, $option->getTimeout());
        $this->assertSame('host', $option->getHost());
        $this->assertSame(9222, $option->getPort());
    }
}
