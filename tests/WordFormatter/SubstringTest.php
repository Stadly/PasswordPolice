<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\Substring
 * @covers ::<protected>
 * @covers ::<private>
 */
final class SubstringTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructFormatterWithMinLengthConstraint(): void
    {
        $formatter = new Substring(5, null);

        // Force generation of code coverage
        $formatterConstruct = new Substring(5, null);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructFormatterWithMaxLengthConstraint(): void
    {
        $formatter = new Substring(1, 10);

        // Force generation of code coverage
        $formatterConstruct = new Substring(1, 10);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructFormatterWithBothMinLengthAndMaxLengthConstraint(): void
    {
        $formatter = new Substring(5, 10);

        // Force generation of code coverage
        $formatterConstruct = new Substring(5, 10);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithMinLengthConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $formatter = new Substring(0, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithNegativeMinLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $formatter = new Substring(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructFormatterWithMaxLengthConstraintSmallerThanMinLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $formatter = new Substring(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructFormatterWithMinLengthConstraintEqualToMaxLengthConstraint(): void
    {
        $formatter = new Substring(5, 5);

        // Force generation of code coverage
        $formatterConstruct = new Substring(5, 5);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWord(): void
    {
        $formatter = new Substring(1, null);

        self::assertEquals([
            'abc',
            'ab',
            'bc',
            'a',
            'b',
            'c',
        ], iterator_to_array($formatter->apply('abc')), '', 0, 10, true);
    }
    
    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithMinLengthConstraint(): void
    {
        $formatter = new Substring(2, null);
        
        self::assertEquals([
            'abc',
            'ab',
            'bc',
        ], iterator_to_array($formatter->apply('abc')), '', 0, 10, true);
    }
    
    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithMaxLengthConstraint(): void
    {
        $formatter = new Substring(1, 2);
        
        self::assertEquals([
            'ab',
            'bc',
            'a',
            'b',
            'c',
        ], iterator_to_array($formatter->apply('abc')), '', 0, 10, true);
    }
    
    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithBothMinAndMaxLengthConstraint(): void
    {
        $formatter = new Substring(2, 2);
        
        self::assertEquals([
            'ab',
            'bc',
        ], iterator_to_array($formatter->apply('abc')), '', 0, 10, true);
    }
}
