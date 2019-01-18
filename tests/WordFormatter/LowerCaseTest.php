<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\LowerCase
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LowerCaseTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatWord(): void
    {
        $formatter = new LowerCase();

        self::assertSame(['foobar'], iterator_to_array($formatter->apply(['fOoBaR']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWords(): void
    {
        $formatter = new LowerCase();

        self::assertSame(['foo', 'bar'], iterator_to_array($formatter->apply(['fOo', 'BaR']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatUtf8Characters(): void
    {
        $formatter = new LowerCase();

        self::assertSame(['ááæøôëñ'], iterator_to_array($formatter->apply(['áÁæØôËñ']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new LowerCase();

        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        $formatter->setNext($next);

        self::assertSame(['oof', 'rab'], iterator_to_array($formatter->apply(['fOo', 'BaR']), false));
    }
}
