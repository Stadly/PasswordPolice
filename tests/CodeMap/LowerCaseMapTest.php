<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CodeMap\LowerCaseMap
 * @covers ::<private>
 * @covers ::<protected>
 */
final class LowerCaseMapTest extends TestCase
{
    /**
     * @covers ::getLengths
     */
    public function testCanGetLengths(): void
    {
        $codeMap = new LowerCaseMap();

        self::assertEquals([
            1,
        ], $codeMap->getLengths(), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeEmptyString(): void
    {
        $codeMap = new LowerCaseMap();

        self::assertEquals([
            '',
        ], $codeMap->code(''), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeDigit(): void
    {
        $codeMap = new LowerCaseMap();

        self::assertEquals([
            '4',
        ], $codeMap->code('4'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeLowerCaseCharacter(): void
    {
        $codeMap = new LowerCaseMap();

        self::assertEquals([
            'f',
        ], $codeMap->code('f'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeUpperCaseCharacter(): void
    {
        $codeMap = new LowerCaseMap();

        self::assertEquals([
            'f',
        ], $codeMap->code('F'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeLowerCaseUtf8Character(): void
    {
        $codeMap = new LowerCaseMap();

        self::assertEquals([
            'ñ',
        ], $codeMap->code('ñ'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeUpperCaseUtf8Character(): void
    {
        $codeMap = new LowerCaseMap();

        self::assertEquals([
            'ë',
        ], $codeMap->code('Ë'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeString(): void
    {
        $codeMap = new LowerCaseMap();

        self::assertEquals([
            'foobar ááæøôëñ 123',
        ], $codeMap->code('fOoBaR áÁæØôËñ 123'), '', 0, 10, true);
    }
}
