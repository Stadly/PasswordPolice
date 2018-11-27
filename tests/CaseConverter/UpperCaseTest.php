<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CaseConverter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CaseConverter\UpperCase
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

        self::assertSame('FOOBAR', $converter->convert('fOoBaR'));
    }
}
