<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CharacterConverter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CharacterConverter\Leet
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LeetTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructConverter(): void
    {
        $converter = new Leet();

        // Force generation of code coverage
        $converterConstruct = new Leet();
        self::assertEquals($converter, $converterConstruct);
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertWordWithoutLeet(): void
    {
        $converter = new Leet();

        self::assertSame(['fOoBaR'], iterator_to_array($converter->convert('fOoBaR')));
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertWordWithLeet(): void
    {
        $converter = new Leet();

        self::assertContains('LEET SPEAK', iterator_to_array($converter->convert('1337 5P34K')));
    }
}
