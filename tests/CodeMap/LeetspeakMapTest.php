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
}
