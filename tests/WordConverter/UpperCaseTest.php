<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordConverter\UpperCase
 * @covers ::<protected>
 * @covers ::<private>
 */
final class UpperCaseTest extends TestCase
{
    /**
     * @covers ::convert
     */
    public function testCanConvertWord(): void
    {
        $converter = new UpperCase();

        self::assertSame(['FOOBAR'], iterator_to_array($converter->convert('fOoBaR')));
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertUtf8Characters(): void
    {
        $converter = new UpperCase();

        self::assertSame(['ÁÁÆØÔËÑ'], iterator_to_array($converter->convert('áÁæØôËñ')));
    }
}
