<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\MixedCaseConverter
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class MixedCaseConverterTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterPath(): void
    {
        $formatter = new MixedCaseConverter();

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('foo'),
            CharTree::fromString('foO'),
            CharTree::fromString('fOo'),
            CharTree::fromString('fOO'),
            CharTree::fromString('Foo'),
            CharTree::fromString('FoO'),
            CharTree::fromString('FOo'),
            CharTree::fromString('FOO'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new MixedCaseConverter();

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('foo'),
            CharTree::fromString('foO'),
            CharTree::fromString('fOo'),
            CharTree::fromString('fOO'),
            CharTree::fromString('Foo'),
            CharTree::fromString('FoO'),
            CharTree::fromString('FOo'),
            CharTree::fromString('FOO'),
            CharTree::fromString('bar'),
            CharTree::fromString('baR'),
            CharTree::fromString('bAr'),
            CharTree::fromString('bAR'),
            CharTree::fromString('Bar'),
            CharTree::fromString('BaR'),
            CharTree::fromString('BAr'),
            CharTree::fromString('BAR'),
            CharTree::fromString('baz'),
            CharTree::fromString('baZ'),
            CharTree::fromString('bAz'),
            CharTree::fromString('bAZ'),
            CharTree::fromString('Baz'),
            CharTree::fromString('BaZ'),
            CharTree::fromString('BAz'),
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
        $formatter = new MixedCaseConverter();

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('áøñ'),
            CharTree::fromString('áøÑ'),
            CharTree::fromString('áØñ'),
            CharTree::fromString('áØÑ'),
            CharTree::fromString('Áøñ'),
            CharTree::fromString('ÁøÑ'),
            CharTree::fromString('ÁØñ'),
            CharTree::fromString('ÁØÑ'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('áØñ'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testResultIsUnique(): void
    {
        $formatter = new MixedCaseConverter();

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('foo'),
            CharTree::fromString('foO'),
            CharTree::fromString('fOo'),
            CharTree::fromString('fOO'),
            CharTree::fromString('Foo'),
            CharTree::fromString('FoO'),
            CharTree::fromString('FOo'),
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
        $formatter = new MixedCaseConverter();

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
            CharTree::fromString('oof'),
            CharTree::fromString('Oof'),
            CharTree::fromString('oOf'),
            CharTree::fromString('OOf'),
            CharTree::fromString('ooF'),
            CharTree::fromString('OoF'),
            CharTree::fromString('oOF'),
            CharTree::fromString('OOF'),
            CharTree::fromString('rab'),
            CharTree::fromString('Rab'),
            CharTree::fromString('rAb'),
            CharTree::fromString('RAb'),
            CharTree::fromString('raB'),
            CharTree::fromString('RaB'),
            CharTree::fromString('rAB'),
            CharTree::fromString('RAB'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }
}
