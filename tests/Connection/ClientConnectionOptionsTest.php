<?php

declare(strict_types=1);

namespace Connection;

use EJTJ3\PhpNats\Connection\ClientConnectionOptions;
use EJTJ3\PhpNats\Constant\Nats;
use Generator;
use PHPUnit\Framework\TestCase;

final class ClientConnectionOptionsTest extends TestCase
{
    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testVerbose(ClientConnectionOptions $options): void
    {
        $options->setVerbose(true);
        $this->assertTrue($options->isVerbose());

        $options->setVerbose(false);
        $this->assertFalse($options->isVerbose());
    }

    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testPedantic(ClientConnectionOptions $options): void
    {
        $options->setPedantic(true);
        $this->assertTrue($options->isPedantic());

        $options->setPedantic(false);
        $this->assertFalse($options->isPedantic());
    }

    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testTlsRequired(ClientConnectionOptions $options): void
    {
        $options->setTlsRequired(true);
        $this->assertTrue($options->isTlsRequired());

        $options->setTlsRequired(false);
        $this->assertFalse($options->isTlsRequired());
    }

    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testSetAuthToken(ClientConnectionOptions $options): void
    {
        $options->setAuthToken('randomstring');
        $this->assertSame('randomstring', $options->getAuthToken());

        $options->setAuthToken(null);
        $this->assertEmpty($options->getAuthToken());
    }

    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testUser(ClientConnectionOptions $options): void
    {
        $options->setUser('admin');
        $this->assertSame('admin', $options->getUser());

        $options->setUser(null);
        $this->assertEmpty($options->getUser());
    }

    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testPassword(ClientConnectionOptions $options): void
    {
        $options->setPassword('randomstring');
        $this->assertSame('randomstring', $options->getPassword());

        $options->setPassword(null);
        $this->assertEmpty($options->getPassword());
    }

    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testName(ClientConnectionOptions $options): void
    {
        $options->setName('publicName');
        $this->assertSame('publicName', $options->getName());

        $options->setName(null);
        $this->assertEmpty($options->getName());
    }

    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testProtocol(ClientConnectionOptions $options): void
    {
        $options->setProtocol(1);
        $this->assertSame(1, $options->getProtocol());

        $options->setProtocol(0);
        $this->assertSame(0, $options->getProtocol());
    }

    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testEcho(ClientConnectionOptions $options): void
    {
        $options->setEcho(true);
        $this->assertTrue($options->isEcho());

        $options->setEcho(false);
        $this->assertFalse($options->isEcho());
    }

    /**
     * @dataProvider createClientConnectionOptions
     */
    public function testToArray(ClientConnectionOptions $options): void
    {
        $expected = [
            'verbose' => true,
            'pedantic' => true,
            'tls_required' => true,
            'auth_token' => 'authToken',
            'user' => 'user',
            'pass' => 'password',
            'name' => 'testing',
            'lang' => 'php',
            'version' => Nats::VERSION,
            'protocol' => 1,
            'echo' => true,
            'no_responders' => true,
            'headers' => true,
        ];

        $options->setEcho(true);
        $options->setProtocol(1);
        $options->setName('testing');
        $options->setUser('user');
        $options->setPassword('password');
        $options->setVerbose(true);
        $options->setAuthToken('authToken');
        $options->setTlsRequired(true);
        $options->setPedantic(true);
        $options->setNoResponders(true);
        $options->setHeaders(true);

        $this->assertSame($expected, $options->toArray());
    }

    public function createClientConnectionOptions(): Generator
    {
        yield [new ClientConnectionOptions()];
    }
}
