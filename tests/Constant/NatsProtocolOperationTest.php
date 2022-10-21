<?php

declare(strict_types=1);

namespace Constant;

use EJTJ3\PhpNats\Constant\NatsProtocolOperation;
use PHPUnit\Framework\TestCase;

final class NatsProtocolOperationTest extends TestCase
{
    public function testIsOperation()
    {
        self::assertTrue(NatsProtocolOperation::Pub->isOperation('PUB'));
        self::assertFalse(NatsProtocolOperation::Pub->isOperation('PUS'));
    }
}
