<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\LengthFilter
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class LengthFilterTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructFormatterWithMinLengthConstraintEqualToZero(): void
    {
        $formatter = new LengthFilter(0, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithNegativeMinLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $formatter = new LengthFilter(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithMaxLengthConstraintSmallerThanMinLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $formatter = new LengthFilter(10, 5);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructFormatterWithMinLengthConstraintEqualToMaxLengthConstraint(): void
    {
        $formatter = new LengthFilter(5, 5);
    }

    /**
     * @covers ::apply
     */
    public function testTheSameIsReturnedWhenAnythingIsAllowed(): void
    {
        $formatter = new LengthFilter(0, null);

        $charTree = CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('Ba'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString(''),
            CharTree::fromString('1'),
            CharTree::fromString('123'),
        ]);
        self::assertSame($charTree, $formatter->apply($charTree));
    }

    /**
     * @covers ::apply
     */
    public function testTheSameIsReturnedWhenFilteringEmpty(): void
    {
        $formatter = new LengthFilter(0, 2);

        $charTree = CharTree::fromNothing();
        self::assertSame($charTree, $formatter->apply($charTree));
    }

    /**
     * @covers ::apply
     */
    public function testEmptyStringIsReturnedWhenMaxLengthIsZeroAndCharTreeContainsEmptyString(): void
    {
        $formatter = new LengthFilter(0, 0);

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
    public function testNothingIsReturnedWhenMaxLengthIsZeroAndCharTreeDoesNotContainEmptyString(): void
    {
        $formatter = new LengthFilter(0, 0);

        self::assertSame(CharTree::fromArray([
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('Ba'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString('1'),
            CharTree::fromString('123'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTreeWhenMaxLengthIsUnbounded(): void
    {
        $formatter = new LengthFilter(2, null);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('Ba'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString('123'),
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
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new LengthFilter(1, 2);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('Ba'),
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
        $formatter = new LengthFilter(2, 3);

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
            CharTree::fromString('oOf'),
            CharTree::fromString('RaB'),
            CharTree::fromString('zaB'),
            CharTree::fromString('aB'),
            CharTree::fromString('321'),
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
