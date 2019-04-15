<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTime;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\DateFormatter\Chaining
 * @covers ::<private>
 * @covers ::<protected>
 */
final class ChainingTest extends TestCase
{
    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanSetAndGetNext(): void
    {
        $formatter = new ChainingClass();

        $next = $this->createMock(Formatter::class);

        $formatter->setNext($next);

        self::assertSame($next, $formatter->getNext());
    }

    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanSetAndGetNullNext(): void
    {
        $formatter = new ChainingClass();

        $next = $this->createMock(Formatter::class);

        $formatter->setNext($next);
        $formatter->setNext(null);

        self::assertNull($formatter->getNext());
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new ChainingClass();

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
            CharTree::fromString('7891/20/90'),
        ]), $formatter->apply([
            new DateTime('2001-02-03'),
            new DateTime('1987-02-09'),
        ]));
    }
}
