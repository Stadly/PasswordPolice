<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordConverter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordConverter\Series
 * @covers ::<protected>
 * @covers ::<private>
 */
final class SeriesTest extends TestCase
{
    /**
     * @var MockObject&WordConverter
     */
    private $wordConverter1;

    /**
     * @var MockObject&WordConverter
     */
    private $wordConverter2;

    protected function setUp(): void
    {
        $this->wordConverter1 = $this->createMock(WordConverter::class);
        $this->wordConverter1->method('convert')->willReturnCallback(
            static function ($word) {
                yield str_replace('4', 'a', $word);
                yield str_replace('1', 'i', $word);
            }
        );

        $this->wordConverter2 = $this->createMock(WordConverter::class);
        $this->wordConverter2->method('convert')->willReturnCallback(
            static function ($word) {
                yield str_replace('3', 'e', $word);
                yield str_replace('1', 'l', $word);
            }
        );
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructSeriesConverterWithZeroWordConverters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $converter = new Series();
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructSeriesConverterWithSingleWordConverter(): void
    {
        $converter = new Series($this->wordConverter1);

        // Force generation of code coverage
        $converterConstruct = new Series($this->wordConverter1);
        self::assertEquals($converter, $converterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructSeriesConverterWithMultipleWordConverters(): void
    {
        $converter = new Series($this->wordConverter1, $this->wordConverter2);

        // Force generation of code coverage
        $converterConstruct = new Series($this->wordConverter1, $this->wordConverter2);
        self::assertEquals($converter, $converterConstruct);
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertWordUsingSingleWordConverter(): void
    {
        $converter = new Series($this->wordConverter1);

        self::assertEquals([
            'ha11o 1337',
            'h4iio i337',
        ], iterator_to_array($converter->convert('h411o 1337'), false), '', 0, 10, true);
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertWordUsingMultipleWordConverters(): void
    {
        $converter = new Series($this->wordConverter1, $this->wordConverter2);

        self::assertEquals([
            'ha11o 1ee7',
            'hallo l337',
            'h4iio iee7',
            'h4iio i337',
        ], iterator_to_array($converter->convert('h411o 1337'), false), '', 0, 10, true);
    }
}
