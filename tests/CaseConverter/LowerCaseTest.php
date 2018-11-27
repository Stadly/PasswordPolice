<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CaseConverter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CaseConverter\LowerCase
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

        self::assertSame('foobar', $converter->convert('fOoBaR'));
    }
}
