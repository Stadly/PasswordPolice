<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\SubstringGenerator
 * @covers ::<protected>
 * @covers ::<private>
 * @covers ::__construct
 */
final class SubstringGeneratorTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithMinLengthConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

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
    public function testCanFormatWordsWhenFilteringUnique(): void
    {
        $formatter = new SubstringGenerator(2, 3, true);

        self::assertEquals([
            'Foo',
            'Fo',
            'oo',
            'bar',
            'ba',
            'ar',
            'foo',
            'oob',
            'oba',
            'fo',
            'ob',
        ], iterator_to_array($formatter->apply([
            'Foo',
            'bar',
            'foobar',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordsWhenNotFilteringUnique(): void
    {
        $formatter = new SubstringGenerator(2, 3, false);

        self::assertEquals([
            'Foo',
            'Fo',
            'oo',
            'bar',
            'ba',
            'ar',
            'foo',
            'oob',
            'oba',
            'bar',
            'fo',
            'oo',
            'ob',
            'ba',
            'ar',
        ], iterator_to_array($formatter->apply([
            'Foo',
            'bar',
            'foobar',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new SubstringGenerator(1, null);
        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        $formatter->setNext($next);

        self::assertEquals([
            'cba',
            'ba',
            'cb',
            'a',
            'b',
            'c',
            'fed',
            'ed',
            'fe',
            'd',
            'e',
            'f',
        ], iterator_to_array($formatter->apply([
            'abc',
            'def',
        ]), false), '', 0, 10, true);
    }
}
