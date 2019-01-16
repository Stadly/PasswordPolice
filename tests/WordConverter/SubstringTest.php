<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordConverter\Substring
 * @covers ::<protected>
 * @covers ::<private>
 */
final class SubstringTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructConverterWithMinLengthConstraint(): void
    {
        $converter = new Substring(5, null);

        // Force generation of code coverage
        $converterConstruct = new Substring(5, null);
        self::assertEquals($converter, $converterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConverterWithMaxLengthConstraint(): void
    {
        $converter = new Substring(1, 10);

        // Force generation of code coverage
        $converterConstruct = new Substring(1, 10);
        self::assertEquals($converter, $converterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConverterWithBothMinLengthAndMaxLengthConstraint(): void
    {
        $converter = new Substring(5, 10);

        // Force generation of code coverage
        $converterConstruct = new Substring(5, 10);
        self::assertEquals($converter, $converterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConverterWithMinLengthConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $converter = new Substring(0, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConverterWithNegativeMinLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $converter = new Substring(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConverterWithMaxLengthConstraintSmallerThanMinLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $converter = new Substring(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConverterWithMinLengthConstraintEqualToMaxLengthConstraint(): void
    {
        $converter = new Substring(5, 5);

        // Force generation of code coverage
        $converterConstruct = new Substring(5, 5);
        self::assertEquals($converter, $converterConstruct);
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertWord(): void
    {
        $converter = new Substring(1, null);

        self::assertEquals([
            'abc',
            'ab',
            'bc',
            'a',
            'b',
            'c',
        ], iterator_to_array($converter->convert('abc')), '', 0, 10, true);
    }
    
    /**
     * @covers ::convert
     */
    public function testCanConvertWordWithMinLengthConstraint(): void
    {
        $converter = new Substring(2, null);
        
        self::assertEquals([
            'abc',
            'ab',
            'bc',
        ], iterator_to_array($converter->convert('abc')), '', 0, 10, true);
    }
    
    /**
     * @covers ::convert
     */
    public function testCanConvertWordWithMaxLengthConstraint(): void
    {
        $converter = new Substring(1, 2);
        
        self::assertEquals([
            'ab',
            'bc',
            'a',
            'b',
            'c',
        ], iterator_to_array($converter->convert('abc')), '', 0, 10, true);
    }
    
    /**
     * @covers ::convert
     */
    public function testCanConvertWordWithBothMinAndMaxLengthConstraint(): void
    {
        $converter = new Substring(2, 2);
        
        self::assertEquals([
            'ab',
            'bc',
        ], iterator_to_array($converter->convert('abc')), '', 0, 10, true);
    }
}
