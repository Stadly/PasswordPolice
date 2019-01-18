<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;

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
}
