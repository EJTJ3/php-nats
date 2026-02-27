<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\HMsg;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class HMsgTest extends TestCase
{
    public function testCreateWithoutReplyTo(): void
    {
        $msg = HMsg::create('subject abc123 10 20');

        $this->assertSame('subject', $msg->subject);
        $this->assertSame('abc123', $msg->subscriptionId);
        $this->assertNull($msg->replyTo);
        $this->assertSame(10, $msg->headerBytes);
        $this->assertSame(20, $msg->totalBytes);
    }

    public function testCreateWithReplyTo(): void
    {
        $msg = HMsg::create('subject abc123 reply.inbox 10 20');

        $this->assertSame('subject', $msg->subject);
        $this->assertSame('abc123', $msg->subscriptionId);
        $this->assertSame('reply.inbox', $msg->replyTo);
        $this->assertSame(10, $msg->headerBytes);
        $this->assertSame(20, $msg->totalBytes);
    }

    public function testSetHeadersWithStatusOnly(): void
    {
        $msg = HMsg::create('subject abc123 10 20');

        $msg->setHeaders("NATS/1.0 503\r\n");

        $this->assertSame(503, $msg->getHeader('status'));
    }

    public function testSetHeadersWithKeyValuePairs(): void
    {
        $msg = HMsg::create('subject abc123 50 100');

        $headers = "NATS/1.0 200\r\nX-Custom: value1\r\nX-Another: value2\r\n";

        $msg->setHeaders($headers);

        $this->assertSame(200, $msg->getHeader('status'));
        $this->assertSame('value1', $msg->getHeader('X-Custom'));
        $this->assertSame('value2', $msg->getHeader('X-Another'));
    }

    public function testSetHeadersWithoutStatus(): void
    {
        $msg = HMsg::create('subject abc123 50 100');

        $headers = "NATS/1.0\r\nX-Custom: value\r\n";

        $msg->setHeaders($headers);

        $this->assertNull($msg->getHeader('status'));
        $this->assertSame('value', $msg->getHeader('X-Custom'));
    }

    public function testGetHeaderReturnsNullForMissing(): void
    {
        $msg = HMsg::create('subject abc123 10 20');

        $this->assertNull($msg->getHeader('nonexistent'));
    }

    public function testGetHeaders(): void
    {
        $msg = HMsg::create('subject abc123 50 100');

        $headers = "NATS/1.0 200\r\nFoo: bar\r\n";
        $msg->setHeaders($headers);

        $allHeaders = $msg->getHeaders();

        $this->assertSame(200, $allHeaders['status']);
        $this->assertSame('bar', $allHeaders['Foo']);
    }

    public function testSetAndGetPayload(): void
    {
        $msg = HMsg::create('subject abc123 10 20');

        $this->assertSame('', $msg->getPayload());

        $msg->setPayload('hello');

        $this->assertSame('hello', $msg->getPayload());
    }

    public function testCreateInvalidFormatThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        HMsg::create('subject');
    }
}
