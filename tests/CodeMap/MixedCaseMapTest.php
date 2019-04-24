<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CodeMap\MixedCaseMap
 * @covers ::<private>
 * @covers ::<protected>
 */
final class MixedCaseMapTest extends TestCase
{
    /**
     * @covers ::getLengths
     */
    public function testCanGetLengths(): void
    {
        $codeMap = new MixedCaseMap();

        self::assertEquals([
            1,
        ], $codeMap->getLengths(), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeEmptyString(): void
    {
        $codeMap = new MixedCaseMap();

        self::assertEquals([
            '',
        ], $codeMap->code(''), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeDigit(): void
    {
        $codeMap = new MixedCaseMap();

        self::assertEquals([
            '4',
        ], $codeMap->code('4'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeLowerCaseCharacter(): void
    {
        $codeMap = new MixedCaseMap();

        self::assertEquals([
            'f',
            'F',
        ], $codeMap->code('f'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeUpperCaseCharacter(): void
    {
        $codeMap = new MixedCaseMap();

        self::assertEquals([
            'f',
            'F',
        ], $codeMap->code('F'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeLowerCaseUtf8Character(): void
    {
        $codeMap = new MixedCaseMap();

        self::assertEquals([
            'ñ',
            'Ñ',
        ], $codeMap->code('ñ'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeUpperCaseUtf8Character(): void
    {
        $codeMap = new MixedCaseMap();

        self::assertEquals([
            'ë',
            'Ë',
        ], $codeMap->code('Ë'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanCodeString(): void
    {
        $codeMap = new MixedCaseMap();

        self::assertEquals([
            'foobar ááæøôëñ 123',
            'FOOBAR ÁÁÆØÔËÑ 123',
        ], $codeMap->code('fOoBaR áÁæØôËñ 123'), '', 0, 10, true);
    }
}
