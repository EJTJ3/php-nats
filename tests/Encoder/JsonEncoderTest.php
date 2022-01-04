<?php

declare(strict_types=1);

namespace Encoder;

use EJTJ3\PhpNats\Encoder\JsonEncoder;
use PHPUnit\Framework\TestCase;

final class JsonEncoderTest extends TestCase
{
    public function testJsonEncode(): void
    {
        $result = '{"config":"builders","are":["cool","nice","handy"]}';
        $data = [
            'config' => 'builders',
            'are' => [
                'cool', 'nice', 'handy',
            ],
        ];

        $encoder = new JsonEncoder();
        $this->assertSame($result, $encoder->encode($data));
        $this->assertSame(json_last_error(), JSON_ERROR_NONE);
    }

    public function testJsonDecode(): void
    {
        $data = '{"config":"builders","are":["cool","nice","handy"]}';
        $result = [
            'config' => 'builders',
            'are' => [
                'cool', 'nice', 'handy',
            ],
        ];

        $encoder = new JsonEncoder();
        $this->assertSame($result, $encoder->decode($data));
        $this->assertSame(json_last_error(), JSON_ERROR_NONE);
    }
}
