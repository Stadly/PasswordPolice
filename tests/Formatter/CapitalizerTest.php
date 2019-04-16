<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\Capitalizer
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class CapitalizerTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatEmptyCharacterTree(): void
    {
        $formatter = new Capitalizer();

        self::assertSame(CharTree::fromArray([
        ]), $formatter->apply(CharTree::fromArray([
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatEmptyStringCharacterTree(): void
    {
        $formatter = new Capitalizer();

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
        $formatter = new Capitalizer();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('Foobar'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOoBaR'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new Capitalizer();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('Foo'),
            CharTree::fromString('Bar'),
            CharTree::fromString('Baz'),
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
        $formatter = new Capitalizer();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('Ááæøôëñ'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('áÁæØôËñ'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testResultIsUnique(): void
    {
        $formatter = new Capitalizer();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('Foo'),
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
        $formatter = new Capitalizer();

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
            CharTree::fromString('ooF'),
            CharTree::fromString('raB'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }
}
