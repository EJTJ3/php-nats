<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\Msg;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class MsgTest extends TestCase
{
    public function testCreateWithoutReplyTo(): void
    {
        $msg = Msg::create('subject abc123 11');

        $this->assertSame('subject', $msg->subject);
        $this->assertSame('abc123', $msg->sid);
        $this->assertNull($msg->replyTo);
        $this->assertSame(11, $msg->bytes);
    }

    public function testCreateWithReplyTo(): void
    {
        $msg = Msg::create('subject abc123 reply.inbox 11');

        $this->assertSame('subject', $msg->subject);
        $this->assertSame('abc123', $msg->sid);
        $this->assertSame('reply.inbox', $msg->replyTo);
        $this->assertSame(11, $msg->bytes);
    }

    public function testSetAndGetPayload(): void
    {
        $msg = Msg::create('subject abc123 5');

        $this->assertSame('', $msg->getPayload());

        $msg->setPayload('hello');

        $this->assertSame('hello', $msg->getPayload());
    }

    public function testCreateInvalidFormatThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Msg::create('subject');
    }
}
