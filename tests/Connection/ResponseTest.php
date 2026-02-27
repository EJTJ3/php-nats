<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\Acknowledgement;
use EJTJ3\PhpNats\Connection\Error;
use EJTJ3\PhpNats\Connection\HMsg;
use EJTJ3\PhpNats\Connection\Msg;
use EJTJ3\PhpNats\Connection\Ping;
use EJTJ3\PhpNats\Connection\Pong;
use EJTJ3\PhpNats\Connection\Response;
use EJTJ3\PhpNats\Connection\ServerInfo;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ResponseTest extends TestCase
{
    public function testParsePong(): void
    {
        $response = Response::parse('PONG');

        $this->assertInstanceOf(Pong::class, $response);
    }

    public function testParsePing(): void
    {
        $response = Response::parse('PING');

        $this->assertInstanceOf(Ping::class, $response);
    }

    public function testParseAck(): void
    {
        $response = Response::parse('+OK');

        $this->assertInstanceOf(Acknowledgement::class, $response);
    }

    public function testParseError(): void
    {
        $response = Response::parse("-ERR 'Unknown Protocol Operation'");

        $this->assertInstanceOf(Error::class, $response);
        $this->assertSame('Unknown Protocol Operation', $response->getMessage());
    }

    public function testParseErrorEmpty(): void
    {
        $response = Response::parse('-ERR');

        $this->assertInstanceOf(Error::class, $response);
        $this->assertSame('', $response->getMessage());
    }

    public function testParseMsg(): void
    {
        $response = Response::parse('MSG subject 1 5');

        $this->assertInstanceOf(Msg::class, $response);
        $this->assertSame('subject', $response->subject);
        $this->assertSame('1', $response->sid);
        $this->assertNull($response->replyTo);
        $this->assertSame(5, $response->bytes);
    }

    public function testParseMsgWithReplyTo(): void
    {
        $response = Response::parse('MSG subject 1 reply.to 5');

        $this->assertInstanceOf(Msg::class, $response);
        $this->assertSame('subject', $response->subject);
        $this->assertSame('1', $response->sid);
        $this->assertSame('reply.to', $response->replyTo);
        $this->assertSame(5, $response->bytes);
    }

    public function testParseHMsg(): void
    {
        $response = Response::parse('HMSG subject 1 10 20');

        $this->assertInstanceOf(HMsg::class, $response);
        $this->assertSame('subject', $response->subject);
        $this->assertSame('1', $response->subscriptionId);
        $this->assertNull($response->replyTo);
        $this->assertSame(10, $response->headerBytes);
        $this->assertSame(20, $response->totalBytes);
    }

    public function testParseHMsgWithReplyTo(): void
    {
        $response = Response::parse('HMSG subject 1 reply.to 10 20');

        $this->assertInstanceOf(HMsg::class, $response);
        $this->assertSame('subject', $response->subject);
        $this->assertSame('1', $response->subscriptionId);
        $this->assertSame('reply.to', $response->replyTo);
        $this->assertSame(10, $response->headerBytes);
        $this->assertSame(20, $response->totalBytes);
    }

    public function testParseInfo(): void
    {
        $json = json_encode([
            'server_id' => 'test-id',
            'version' => '2.0.0',
            'go' => 'go1.19',
            'host' => '0.0.0.0',
            'port' => 4222,
            'proto' => 1,
            'max_payload' => 1048576,
        ]);

        $response = Response::parse('INFO ' . $json);

        $this->assertInstanceOf(ServerInfo::class, $response);
        $this->assertSame('test-id', $response->getServerId());
        $this->assertSame('2.0.0', $response->getVersion());
    }

    public function testParseInvalidResponseThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        Response::parse('INVALID');
    }

    public function testParseUnsupportedOperationThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        Response::parse('UNKNOWN something');
    }
}
