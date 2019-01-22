<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTime;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\DateFormatter;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\DateFormatter\DefaultFormatter
 * @covers ::<protected>
 * @covers ::<private>
 * @covers ::__construct
 */
final class DefaultFormatterTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatDates(): void
    {
        $formatter = new DefaultFormatter();

        $expected = [
            '2001',
            '2/03',
            '3. 02',
            '19-11',
            '11,07',
            '07, 11',
        ];

        self::assertEquals($expected, array_unique(array_intersect(iterator_to_array($formatter->apply([
            new DateTime('2001-02-03 07:11:19'),
            new DateTime('1907-11-19 21:13:58'),
        ]), false), $expected)), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new DefaultFormatter();

        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        $formatter->setNext($next);

        $expected = [
            '1002',
            '30/2',
            '20 .3',
            '11-91',
            '70,11',
            '11 ,70',
        ];

        self::assertEquals($expected, array_unique(array_intersect(iterator_to_array($formatter->apply([
            new DateTime('2001-02-03 07:11:19'),
            new DateTime('1907-11-19 21:13:58'),
        ]), false), $expected)), '', 0, 10, true);
    }
}
