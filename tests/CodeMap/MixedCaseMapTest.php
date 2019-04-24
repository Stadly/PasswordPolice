<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CodeMap\MixedCaseMap
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class MixedCaseMapTest extends TestCase
{
    /**
     * @covers ::getMap
     */
    public function testCanGetCodeMap(): void
    {
        $codeMap = new MixedCaseMap();

        $charTree = CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString('123'),
            CharTree::fromString('áÁ'),
            CharTree::fromString('æ'),
            CharTree::fromString('Øô'),
            CharTree::fromString('Ëñ'),
        ]);
        self::assertEquals([
            'f' => ['f', 'F'],
            'B' => ['b', 'B'],
            '1' => ['1'],
            'á' => ['á', 'Á'],
            'æ' => ['æ', 'Æ'],
            'Ø' => ['ø', 'Ø'],
            'Ë' => ['ë', 'Ë'],
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

    /**
     * @covers ::getMap
     */
    public function testCanGetCodeMapForEmpty(): void
    {
        $codeMap = new MixedCaseMap();

        $charTree = CharTree::fromArray([
        ]);
        self::assertEquals([
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

    /**
     * @covers ::getMap
     */
    public function testCanGetCodeMapForEmptyString(): void
    {
        $codeMap = new MixedCaseMap();

        $charTree = CharTree::fromArray([
            CharTree::fromString(''),
        ]);
        self::assertEquals([
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

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
