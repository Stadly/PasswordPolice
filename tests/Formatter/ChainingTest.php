<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\Chaining
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
                    $charTrees[] = CharTree::fromString(ucfirst($string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $formatter->setNext($next);

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('OOf'),
            CharTree::fromString('RaB'),
            CharTree::fromString('ZaB'),
            CharTree::fromString('321'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString('123'),
        ])));
    }
}
