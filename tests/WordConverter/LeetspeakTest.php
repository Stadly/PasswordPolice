<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordConverter\Leetspeak
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LeetspeakTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructConverter(): void
    {
        $converter = new Leetspeak();

        // Force generation of code coverage
        $converterConstruct = new Leetspeak();
        self::assertEquals($converter, $converterConstruct);
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertWordWithoutLeetspeak(): void
    {
        $converter = new Leetspeak();

        self::assertSame(['fOoBaR'], iterator_to_array($converter->convert('fOoBaR')));
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertWordWithLeetspeak(): void
    {
        $converter = new Leetspeak();

        self::assertContains('LEET SPEAK', iterator_to_array($converter->convert('1337 5P34K')));
    }
}
