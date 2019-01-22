<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\DateFormatter;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\DateFormatter\FormatterCombiner
 * @covers ::<protected>
 * @covers ::<private>
 * @covers ::__construct
 */
final class FormatterCombinerTest extends TestCase
{
    /**
     * @var MockObject&DateFormatter
     */
    private $dateFormatter1;

    /**
     * @var MockObject&DateFormatter
     */
    private $dateFormatter2;

    protected function setUp(): void
    {
        $this->dateFormatter1 = $this->createMock(DateFormatter::class);
        $this->dateFormatter1->method('apply')->willReturnCallback(
            static function (iterable $dates): Traversable {
                foreach ($dates as $date) {
                    yield $date->format('d/m/Y');
                    yield $date->format('m/d/y');
                }
            }
        );

        $this->dateFormatter2 = $this->createMock(DateFormatter::class);
        $this->dateFormatter2->method('apply')->willReturnCallback(
            static function (iterable $dates): Traversable {
                foreach ($dates as $date) {
                    yield $date->format('H:i:s');
                    yield $date->format('i/s/H');
                }
            }
        );
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatDatesWhenFilteringUnique(): void
    {
        $formatter = new FormatterCombiner([$this->dateFormatter1, $this->dateFormatter2], true);

        self::assertEquals([
            '03/02/2001',
            '02/03/01',
            '07:11:19',
            '11/19/07',
            '19/11/1907',
            '21:13:58',
            '13/58/21',
        ], iterator_to_array($formatter->apply([
            new DateTime('2001-02-03 07:11:19'),
            new DateTime('1907-11-19 21:13:58'),
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatDatesWhenNotFilteringUnique(): void
    {
        $formatter = new FormatterCombiner([$this->dateFormatter1, $this->dateFormatter2], false);

        self::assertEquals([
            '03/02/2001',
            '02/03/01',
            '07:11:19',
            '11/19/07',
            '19/11/1907',
            '11/19/07',
            '21:13:58',
            '13/58/21',
        ], iterator_to_array($formatter->apply([
            new DateTime('2001-02-03 07:11:19'),
            new DateTime('1907-11-19 21:13:58'),
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new FormatterCombiner([$this->dateFormatter1, $this->dateFormatter2], false);

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
            '1002/20/30',
            '10/30/20',
            '91:11:70',
            '70/91/11',
            '7091/11/91',
            '70/91/11',
            '85:31:12',
            '12/85/31',
        ], iterator_to_array($formatter->apply([
            new DateTime('2001-02-03 07:11:19'),
            new DateTime('1907-11-19 21:13:58'),
        ]), false), '', 0, 10, true);
    }
}
