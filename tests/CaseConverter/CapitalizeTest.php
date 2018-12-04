<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CaseConverter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CaseConverter\Capitalize
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

        self::assertSame('Foobar', $converter->convert('fOoBaR'));
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertUtf8Characters(): void
    {
        $converter = new Capitalize();

        self::assertSame('Ááæøôëñ', $converter->convert('áÁæØôËñ'));
    }
}
