<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CodeMap\LeetspeakMap
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class LeetspeakMapTest extends TestCase
{
    /**
     * @covers ::getMap
     */
    public function testCanGetEncodeMap(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

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
            'f' => ['f', 'ƒ'],
            'B' => ['B', '8', 'ß'],
            '1' => ['1'],
            'á' => ['á'],
            'æ' => ['æ'],
            'Ø' => ['Ø'],
            'Ë' => ['Ë'],
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

    /**
     * @covers ::getMap
     */
    public function testCanGetDecodeMap(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

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
            'f' => ['f'],
            'B' => ['B'],
            '1' => ['1', 'L', 'I'],
            'á' => ['á'],
            'æ' => ['æ'],
            'Ø' => ['Ø'],
            'Ë' => ['Ë'],
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

    /**
     * @covers ::getMap
     */
    public function testCanGetEncodeMapForEmpty(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        $charTree = CharTree::fromArray([
        ]);
        self::assertEquals([
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

    /**
     * @covers ::getMap
     */
    public function testCanGetEncodeMapForEmptyString(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        $charTree = CharTree::fromArray([
            CharTree::fromString(''),
        ]);
        self::assertEquals([
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

    /**
     * @covers ::getMap
     */
    public function testCanGetDecodeMapForEmpty(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        $charTree = CharTree::fromArray([
        ]);
        self::assertEquals([
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

    /**
     * @covers ::getMap
     */
    public function testCanGetDecodeMapForEmptyString(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        $charTree = CharTree::fromArray([
            CharTree::fromString(''),
        ]);
        self::assertEquals([
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

    /**
     * @covers ::getLengths
     */
    public function testCanGetEncodeLengths(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        self::assertEquals([
            1,
        ], $codeMap->getLengths(), '', 0, 10, true);
    }

    /**
     * @covers ::getLengths
     */
    public function testCanGetDecodeLengths(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        self::assertEquals([
            1,
            2,
        ], $codeMap->getLengths(), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanEncodeEmptyString(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        self::assertEquals([
            '',
        ], $codeMap->code(''), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanEncodeDigit(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        self::assertEquals([
            '4',
        ], $codeMap->code('4'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanEncodeLowerCaseCharacter(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        self::assertEquals([
            '6',
            '9',
            'g',
        ], $codeMap->code('g'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanEncodeUpperCaseCharacter(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        self::assertEquals([
            '6',
            '9',
            'G',
        ], $codeMap->code('G'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanEncodeLowerCaseUtf8Character(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        self::assertEquals([
            'ñ',
        ], $codeMap->code('ñ'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanEncodeUpperCaseUtf8Character(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        self::assertEquals([
            'Ë',
        ], $codeMap->code('Ë'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanEncodeMultipleCharacters(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        self::assertEquals([
            'vV',
        ], $codeMap->code('vV'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanEncodeString(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/true);

        self::assertEquals([
            'fOoBaR áÁæØôËñ 123',
        ], $codeMap->code('fOoBaR áÁæØôËñ 123'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanDecodeEmptyString(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        self::assertEquals([
            '',
        ], $codeMap->code(''), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanDecodeDigit(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        self::assertEquals([
            'I',
            'L',
            '1',
        ], $codeMap->code('1'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanDecodeLowerCaseCharacter(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        self::assertEquals([
            'K',
            'x',
        ], $codeMap->code('x'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanDecodeUpperCaseCharacter(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        self::assertEquals([
            'K',
            'X',
        ], $codeMap->code('X'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanDecodeLowerCaseUtf8Character(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        self::assertEquals([
            'ñ',
        ], $codeMap->code('ñ'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanDecodeUpperCaseUtf8Character(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        self::assertEquals([
            'Ë',
        ], $codeMap->code('Ë'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanDecodeMultipleCharacters(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        self::assertEquals([
            'W',
            'vV',
        ], $codeMap->code('vV'), '', 0, 10, true);
    }

    /**
     * @covers ::code
     */
    public function testCanDecodeString(): void
    {
        $codeMap = new LeetspeakMap(/*encode*/false);

        self::assertEquals([
            'fOoBaR áÁæØôËñ 123',
        ], $codeMap->code('fOoBaR áÁæØôËñ 123'), '', 0, 10, true);
    }
}
