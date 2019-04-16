<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\LowerCaseConverter
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class LowerCaseConverterTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatEmptyCharacterTree(): void
    {
        $formatter = new LowerCaseConverter();

        self::assertSame(CharTree::fromArray([
        ]), $formatter->apply(CharTree::fromArray([
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatEmptyStringCharacterTree(): void
    {
        $formatter = new LowerCaseConverter();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString(''),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString(''),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterPath(): void
    {
        $formatter = new LowerCaseConverter();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('foobar'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOoBaR'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new LowerCaseConverter();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('foo'),
            CharTree::fromString('bar'),
            CharTree::fromString('baz'),
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
        $formatter = new LowerCaseConverter();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('ááæøôëñ'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('áÁæØôËñ'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testResultIsUnique(): void
    {
        $formatter = new LowerCaseConverter();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('foo'),
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
        $formatter = new LowerCaseConverter();

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

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('oof'),
            CharTree::fromString('rab'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }
}
