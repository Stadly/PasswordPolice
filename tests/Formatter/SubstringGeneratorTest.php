<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\SubstringGenerator
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class SubstringGeneratorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructFormatterWithMinLengthConstraintEqualToZero(): void
    {
        $formatter = new SubstringGenerator(0, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithNegativeMinLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $formatter = new SubstringGenerator(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithMaxLengthConstraintSmallerThanMinLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $formatter = new SubstringGenerator(10, 5);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructFormatterWithMinLengthConstraintEqualToMaxLengthConstraint(): void
    {
        $formatter = new SubstringGenerator(5, 5);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterPath(): void
    {
        $formatter = new SubstringGenerator(2, 3);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('fO'),
            CharTree::fromString('fOo'),
            CharTree::fromString('Oo'),
            CharTree::fromString('OoB'),
            CharTree::fromString('oB'),
            CharTree::fromString('oBa'),
            CharTree::fromString('Ba'),
            CharTree::fromString('BaR'),
            CharTree::fromString('aR'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOoBaR'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new SubstringGenerator(1, 2);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('f'),
            CharTree::fromString('fO'),
            CharTree::fromString('O'),
            CharTree::fromString('Oo'),
            CharTree::fromString('o'),
            CharTree::fromString('B'),
            CharTree::fromString('Ba'),
            CharTree::fromString('a'),
            CharTree::fromString('aR'),
            CharTree::fromString('R'),
            CharTree::fromString('az'),
            CharTree::fromString('z'),
            CharTree::fromString('1'),
            CharTree::fromString('12'),
            CharTree::fromString('2'),
            CharTree::fromString('23'),
            CharTree::fromString('3'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('Ba'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString('123'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanIncludeEmptySubstring(): void
    {
        $formatter = new SubstringGenerator(0, 1);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString(''),
            CharTree::fromString('f'),
            CharTree::fromString('O'),
            CharTree::fromString('o'),
            CharTree::fromString('B'),
            CharTree::fromString('a'),
            CharTree::fromString('R'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOoBaR'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new SubstringGenerator(1, null);

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
            CharTree::fromString('o'),
            CharTree::fromString('oO'),
            CharTree::fromString('oOf'),
            CharTree::fromString('O'),
            CharTree::fromString('Of'),
            CharTree::fromString('f'),
            CharTree::fromString('R'),
            CharTree::fromString('Ra'),
            CharTree::fromString('RaB'),
            CharTree::fromString('a'),
            CharTree::fromString('aB'),
            CharTree::fromString('B'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }
}
