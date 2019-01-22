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
 * @coversDefaultClass \Stadly\PasswordPolice\DateFormatter\ChainableFormatter
 * @covers ::<protected>
 * @covers ::<private>
 */
final class ChainableFormatterTest extends TestCase
{
    /**
     * @var MockObject&WordFormatter
     */
    private $dateFormatter;

    protected function setUp(): void
    {
        $this->dateFormatter = $this->createMock(WordFormatter::class);
    }

    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanSetAndGetNext(): void
    {
        $formatter = $this->getMockForAbstractClass(ChainableFormatter::class);

        $formatter->setNext($this->dateFormatter);
        self::assertSame($this->dateFormatter, $formatter->getNext());
    }

    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanGetWhenNoNextIsSet(): void
    {
        $formatter = $this->getMockForAbstractClass(ChainableFormatter::class);

        $formatter->setNext($this->dateFormatter);
        $formatter->setNext(null);

        self::assertNull($formatter->getNext());
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatter(): void
    {
        $formatter = $this->getMockForAbstractClass(ChainableFormatter::class);
        $formatter->method('applyCurrent')->willReturnCallback(
            static function (iterable $dates): Traversable {
                foreach ($dates as $date) {
                    yield $date->format('d/m/Y');
                }
            }
        );

        self::assertSame([
            '03/02/2001',
            '09/02/1987',
        ], iterator_to_array($formatter->apply([
            new DateTime('2001-02-03'),
            new DateTime('1987-02-09'),
        ]), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = $this->getMockForAbstractClass(ChainableFormatter::class);
        $formatter->method('applyCurrent')->willReturnCallback(
            static function (iterable $dates): Traversable {
                foreach ($dates as $date) {
                    yield $date->format('d/m/Y');
                }
            }
        );

        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        $formatter->setNext($next);

        self::assertSame([
            '1002/20/30',
            '7891/20/90',
        ], iterator_to_array($formatter->apply([
            new DateTime('2001-02-03'),
            new DateTime('1987-02-09'),
        ]), false));
    }
}
