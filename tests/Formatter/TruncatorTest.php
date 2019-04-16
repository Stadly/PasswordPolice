<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\Truncator
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class TruncatorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructFormatterWithLengthConstraintEqualToZero(): void
    {
        $formatter = new Truncator(0);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithNegativeLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $formatter = new Truncator(-10);
    }

    /**
     * @covers ::apply
     */
    public function testEmptyIsReturnedWhenTruncatingEmpty(): void
    {
        $formatter = new Truncator(3);

        self::assertSame(CharTree::fromArray([
        ]), $formatter->apply(CharTree::fromArray([
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testEmptyIsReturnedWhenTruncatingEmptyToZeroLength(): void
    {
        $formatter = new Truncator(0);

        self::assertSame(CharTree::fromArray([
        ]), $formatter->apply(CharTree::fromArray([
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testEmptyStringIsReturnedWhenTruncatingToZeroLength(): void
    {
        $formatter = new Truncator(0);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString(''),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('Ba'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString(''),
            CharTree::fromString('1'),
            CharTree::fromString('123'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testWholeTreeIsReturnedWhenTruncatingToMaxLength(): void
    {
        $formatter = new Truncator(6);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testWholeTreeIsReturnedWhenTruncatingToLongerThanMaxLength(): void
    {
        $formatter = new Truncator(10);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testTruncatedTreeIsReturnedWhenTruncatingToShorterThanMaxLength(): void
    {
        $formatter = new Truncator(2);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('ac'),
            CharTree::fromString('fo'),
            CharTree::fromString('k'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new Truncator(1);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('f'),
            CharTree::fromString('B'),
            CharTree::fromString(''),
            CharTree::fromString('1'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('Ba'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString(''),
            CharTree::fromString('1'),
            CharTree::fromString('123'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new Truncator(2);

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
            CharTree::fromString('Of'),
            CharTree::fromString('aB'),
            CharTree::fromString(''),
            CharTree::fromString('1'),
            CharTree::fromString('21'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('Ba'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString(''),
            CharTree::fromString('1'),
            CharTree::fromString('123'),
        ])));
    }
}
