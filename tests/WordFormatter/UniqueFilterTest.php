<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\UniqueFilter
 * @covers ::<protected>
 * @covers ::<private>
 */
final class UniqueFilterTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFilterZeroWords(): void
    {
        $formatter = new UniqueFilter();

        self::assertEquals([
        ], iterator_to_array($formatter->apply([]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFilterSatisfiedWords(): void
    {
        $formatter = new UniqueFilter();

        self::assertEquals([
            'a',
            'ab',
            'abc',
            'abcd',
            'ef',
        ], iterator_to_array($formatter->apply([
            'a',
            'ab',
            'abc',
            'abcd',
            'ef',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFilterWords(): void
    {
        $formatter = new UniqueFilter();

        self::assertEquals([
            'a',
            'ab',
            'abc',
            'abcd',
            'ef',
        ], iterator_to_array($formatter->apply([
            'a',
            'ab',
            'a',
            'a',
            'a',
            'a',
            'a',
            'abc',
            'ef',
            'ab',
            'ef',
            'ab',
            'abcd',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new UniqueFilter();

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
            'a',
            'ba',
            'cba',
            'dcba',
            'fe',
        ], iterator_to_array($formatter->apply([
            'a',
            'ab',
            'a',
            'a',
            'a',
            'a',
            'a',
            'abc',
            'ef',
            'ab',
            'ef',
            'ab',
            'abcd',
        ]), false), '', 0, 10, true);
    }
}
