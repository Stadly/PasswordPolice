<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\UpperCase
 * @covers ::<protected>
 * @covers ::<private>
 */
final class UpperCaseTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatWord(): void
    {
        $formatter = new UpperCase();

        self::assertSame(['FOOBAR'], iterator_to_array($formatter->apply(['fOoBaR']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWords(): void
    {
        $formatter = new UpperCase();

        self::assertSame(['FOO', 'BAR'], iterator_to_array($formatter->apply(['fOo' ,'BaR']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatUtf8Characters(): void
    {
        $formatter = new UpperCase();

        self::assertSame(['ÁÁÆØÔËÑ'], iterator_to_array($formatter->apply(['áÁæØôËñ']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new UpperCase();

        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        $formatter->setNext($next);

        self::assertSame(['OOF', 'RAB'], iterator_to_array($formatter->apply(['fOo', 'BaR']), false));
    }
}
