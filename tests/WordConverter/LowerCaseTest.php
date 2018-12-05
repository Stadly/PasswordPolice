<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordConverter\LowerCase
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LowerCaseTest extends TestCase
{
    /**
     * @covers ::convert
     */
    public function testCanConvertWord(): void
    {
        $converter = new LowerCase();

        self::assertSame(['foobar'], iterator_to_array($converter->convert('fOoBaR')));
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertUtf8Characters(): void
    {
        $converter = new LowerCase();

        self::assertSame(['ááæøôëñ'], iterator_to_array($converter->convert('áÁæØôËñ')));
    }
}
