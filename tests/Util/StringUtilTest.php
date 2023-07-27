<?php

namespace Util;

use EJTJ3\PhpNats\Util\StringUtil;
use PHPUnit\Framework\TestCase;

final class StringUtilTest extends TestCase
{
    public function testIsEmpty(): void
    {
        $cases = [null, '', ' ', '  '];

        foreach ($cases as $case) {
            $this->assertTrue(StringUtil::isEmpty($case));
        }
    }

    public function testNonEmptyStrings(): void
    {
        $cases = ['test', ' . ', '123'];

        foreach ($cases as $case) {
            $this->assertFalse(StringUtil::isEmpty($case));
        }
    }

    public function testExplode(): void
    {
        $string = 'MSG reply 2d4a3629 1649';
        $parts = StringUtil::explode($string);

        self::assertEquals([
            'MSG',
            'reply',
            '2d4a3629',
            '1649',
        ], $parts);
    }

    public function testExplodeWithLimit(): void
    {
        $string = 'MSG reply 2d4a3629 1649';
        $parts = StringUtil::explode($string, 2);

        self::assertEquals([
            'MSG',
            'reply 2d4a3629 1649',
        ], $parts);
    }
}
