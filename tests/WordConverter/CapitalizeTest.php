<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordConverter\Capitalize
 * @covers ::<protected>
 * @covers ::<private>
 */
final class CapitalizeTest extends TestCase
{
    /**
     * @covers ::convert
     */
    public function testCanConvertWord(): void
    {
        $converter = new Capitalize();

        self::assertSame(['Foobar'], iterator_to_array($converter->convert('fOoBaR')));
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertUtf8Characters(): void
    {
        $converter = new Capitalize();

        self::assertSame(['Ááæøôëñ'], iterator_to_array($converter->convert('áÁæØôËñ')));
    }
}
