<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\LengthFilter
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LengthFilterTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructFormatterWithMinLengthConstraint(): void
    {
        $formatter = new LengthFilter(5, null);

        // Force generation of code coverage
        $formatterConstruct = new LengthFilter(5, null);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructFormatterWithMaxLengthConstraint(): void
    {
        $formatter = new LengthFilter(1, 10);

        // Force generation of code coverage
        $formatterConstruct = new LengthFilter(1, 10);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructFormatterWithBothMinLengthAndMaxLengthConstraint(): void
    {
        $formatter = new LengthFilter(5, 10);

        // Force generation of code coverage
        $formatterConstruct = new LengthFilter(5, 10);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithMinLengthConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

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
     */
    public function testCanConstructFormatterWithMinLengthConstraintEqualToMaxLengthConstraint(): void
    {
        $formatter = new LengthFilter(5, 5);

        // Force generation of code coverage
        $formatterConstruct = new LengthFilter(5, 5);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::apply
     */
    public function testCanFilterZeroWords(): void
    {
        $formatter = new LengthFilter(1, null);

        self::assertEquals([
        ], iterator_to_array($formatter->apply([]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFilterSatisfiedWords(): void
    {
        $formatter = new LengthFilter(1, 4);

        self::assertEquals([
            'a',
            'ab',
            'abc',
            'abcd',
            'ef',
        ], iterator_to_array($formatter->apply([
            'a',
            'ab',
            'abc',
            'abcd',
            'ef',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFilterUnsatisfiedWords(): void
    {
        $formatter = new LengthFilter(5, null);

        self::assertEquals([
        ], iterator_to_array($formatter->apply([
            'a',
            'ab',
            'abc',
            'abcd',
            'ef',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithMinLengthConstraint(): void
    {
        $formatter = new LengthFilter(2, null);

        self::assertEquals([
            'ab',
            'abc',
            'abcd',
            'ef',
        ], iterator_to_array($formatter->apply([
            'a',
            'ab',
            'abc',
            'abcd',
            'ef',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithMaxLengthConstraint(): void
    {
        $formatter = new LengthFilter(1, 3);

        self::assertEquals([
            'a',
            'ab',
            'abc',
            'ef',
        ], iterator_to_array($formatter->apply([
            'a',
            'ab',
            'abc',
            'abcd',
            'ef',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithBothMinAndMaxLengthConstraint(): void
    {
        $formatter = new LengthFilter(2, 2);

        self::assertEquals([
            'ab',
            'ef',
        ], iterator_to_array($formatter->apply([
            'a',
            'ab',
            'abc',
            'abcd',
            'ef',
        ]), false), '', 0, 10, true);
    }
}
