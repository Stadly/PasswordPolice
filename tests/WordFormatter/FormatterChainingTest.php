<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\FormatterChaining
 * @covers ::<protected>
 * @covers ::<private>
 */
final class FormatterChainingTest extends TestCase
{
    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanSetAndGetNext(): void
    {
        $formatter = new FormatterChainingClass();

        $next = $this->createMock(WordFormatter::class);

        $formatter->setNext($next);

        self::assertSame($next, $formatter->getNext());
    }

    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanGetWhenNoNextIsSet(): void
    {
        $formatter = new FormatterChainingClass();

        $next = $this->createMock(WordFormatter::class);

        $formatter->setNext($next);
        $formatter->setNext(null);

        self::assertNull($formatter->getNext());
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatter(): void
    {
        $formatter = new FormatterChainingClass();

        self::assertSame(['cba', 'fed'], iterator_to_array($formatter->apply(['abc', 'def']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new FormatterChainingClass();

        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strtoupper($word);
                }
            }
        );

        $formatter->setNext($next);

        self::assertSame(['CBA', 'FED'], iterator_to_array($formatter->apply(['abc', 'def']), false));
    }
}
