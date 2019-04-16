<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CodeMap\LowerCaseMap
 * @covers ::<private>
 * @covers ::<protected>
 */
final class LowerCaseMapTest extends TestCase
{
    /**
     * @covers ::getMap
     */
    public function testCanGetCodeMap(): void
    {
        $codeMap = new LowerCaseMap();

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
            'B' => ['b'],
            '1' => ['1'],
            'á' => ['á'],
            'æ' => ['æ'],
            'Ø' => ['ø'],
            'Ë' => ['ë'],
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }

    /**
     * @covers ::getMap
     */
    public function testCanGetCodeMapForEmpty(): void
    {
        $codeMap = new LowerCaseMap();

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
        $codeMap = new LowerCaseMap();

        $charTree = CharTree::fromArray([
            CharTree::fromString(''),
        ]);
        self::assertEquals([
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }
}
