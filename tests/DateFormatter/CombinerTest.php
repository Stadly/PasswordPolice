<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\DateFormatter;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\DateFormatter\Combiner
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class CombinerTest extends TestCase
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
            static function (iterable $dates): CharTree {
                $charTrees = [];
                foreach ($dates as $date) {
                    $charTrees[] = CharTree::fromString($date->format('d/m/Y'));
                    $charTrees[] = CharTree::fromString($date->format('m/d/y'));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $this->dateFormatter2 = $this->createMock(DateFormatter::class);
        $this->dateFormatter2->method('apply')->willReturnCallback(
            static function (iterable $dates): CharTree {
                $charTrees = [];
                foreach ($dates as $date) {
                    $charTrees[] = CharTree::fromString($date->format('H:i:s'));
                    $charTrees[] = CharTree::fromString($date->format('i/s/H'));
                }
                return CharTree::fromArray($charTrees);
            }
        );
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatDates(): void
    {
        $formatter = new Combiner([$this->dateFormatter1, $this->dateFormatter2]);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('03/02/2001'),
            CharTree::fromString('02/03/01'),
            CharTree::fromString('07:11:19'),
            CharTree::fromString('11/19/07'),
            CharTree::fromString('19/11/1907'),
            CharTree::fromString('21:13:58'),
            CharTree::fromString('13/58/21'),
        ]), $formatter->apply([
            new DateTime('2001-02-03 07:11:19'),
            new DateTime('1907-11-19 21:13:58'),
        ]));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new Combiner([$this->dateFormatter1, $this->dateFormatter2]);

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
            CharTree::fromString('1002/20/30'),
            CharTree::fromString('10/30/20'),
            CharTree::fromString('91:11:70'),
            CharTree::fromString('70/91/11'),
            CharTree::fromString('7091/11/91'),
            CharTree::fromString('85:31:12'),
            CharTree::fromString('12/85/31'),
        ]), $formatter->apply([
            new DateTime('2001-02-03 07:11:19'),
            new DateTime('1907-11-19 21:13:58'),
        ]));
    }
}
