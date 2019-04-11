<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\UpperCaseConverter
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class UpperCaseConverterTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterPath(): void
    {
        $formatter = new UpperCaseConverter();

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('FOOBAR'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOoBaR'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new UpperCaseConverter();

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('FOO'),
            CharTree::fromString('BAR'),
            CharTree::fromString('BAZ'),
            CharTree::fromString('123'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString('123'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatUtf8Characters(): void
    {
        $formatter = new UpperCaseConverter();

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('ÁÁÆØÔËÑ'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('áÁæØôËñ'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testResultIsUnique(): void
    {
        $formatter = new UpperCaseConverter();

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('FOO'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('foo'),
            CharTree::fromString('foO'),
            CharTree::fromString('fOo'),
            CharTree::fromString('fOO'),
            CharTree::fromString('Foo'),
            CharTree::fromString('FoO'),
            CharTree::fromString('FOo'),
            CharTree::fromString('FOO'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new UpperCaseConverter();

        $next = $this->createMock(Formatter::class);
        $next->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(strrev($string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $formatter->setNext($next);

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('OOF'),
            CharTree::fromString('RAB'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }
}
