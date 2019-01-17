<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\Series
 * @covers ::<protected>
 * @covers ::<private>
 */
final class SeriesTest extends TestCase
{
    /**
     * @var MockObject&WordFormatter
     */
    private $wordFormatter1;

    /**
     * @var MockObject&WordFormatter
     */
    private $wordFormatter2;

    protected function setUp(): void
    {
        $this->wordFormatter1 = $this->createMock(WordFormatter::class);
        $this->wordFormatter1->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace('4', 'a', $word);
                    yield str_replace('1', 'i', $word);
                }
            }
        );

        $this->wordFormatter2 = $this->createMock(WordFormatter::class);
        $this->wordFormatter2->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace('3', 'e', $word);
                    yield str_replace('1', 'l', $word);
                }
            }
        );
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructSeriesFormatterWithZeroWordFormatters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $formatter = new Series();
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructSeriesFormatterWithSingleWordFormatter(): void
    {
        $formatter = new Series($this->wordFormatter1);

        // Force generation of code coverage
        $formatterConstruct = new Series($this->wordFormatter1);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructSeriesFormatterWithMultipleWordFormatters(): void
    {
        $formatter = new Series($this->wordFormatter1, $this->wordFormatter2);

        // Force generation of code coverage
        $formatterConstruct = new Series($this->wordFormatter1, $this->wordFormatter2);
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordUsingSingleWordFormatter(): void
    {
        $formatter = new Series($this->wordFormatter1);

        self::assertEquals([
            'ha11o 1337',
            'h4iio i337',
        ], iterator_to_array($formatter->apply(['h411o 1337']), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordsUsingSingleWordFormatter(): void
    {
        $formatter = new Series($this->wordFormatter1);

        self::assertEquals([
            'ha11o',
            'h4iio',
            '1337',
            'i337',
        ], iterator_to_array($formatter->apply(['h411o', '1337']), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordUsingMultipleWordFormatters(): void
    {
        $formatter = new Series($this->wordFormatter1, $this->wordFormatter2);

        self::assertEquals([
            'ha11o 1ee7',
            'hallo l337',
            'h4iio iee7',
            'h4iio i337',
        ], iterator_to_array($formatter->apply(['h411o 1337']), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordsUsingMultipleWordFormatters(): void
    {
        $formatter = new Series($this->wordFormatter1, $this->wordFormatter2);

        self::assertEquals([
            'ha11e',
            'hall3',
            'h4iie',
            'h4ii3',
            '1ee7',
            'l337',
            'iee7',
            'i337',
        ], iterator_to_array($formatter->apply(['h4113', '1337']), false), '', 0, 10, true);
    }
}
