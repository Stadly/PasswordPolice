<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;

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

        self::assertSame(['foobar'], iterator_to_array($formatter->apply('fOoBaR')));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatUtf8Characters(): void
    {
        $formatter = new LowerCase();

        self::assertSame(['ááæøôëñ'], iterator_to_array($formatter->apply('áÁæØôËñ')));
    }
}
