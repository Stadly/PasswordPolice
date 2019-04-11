<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CodeMap\UpperCaseMap
 * @covers ::<private>
 * @covers ::<protected>
 */
final class UpperCaseMapTest extends TestCase
{
    /**
     * @covers ::getMap
     */
    public function testCanGetCodeMap(): void
    {
        $codeMap = new UpperCaseMap();

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
            'f' => ['F'],
            'B' => ['B'],
            '1' => ['1'],
            'á' => ['Á'],
            'æ' => ['Æ'],
            'Ø' => ['Ø'],
            'Ë' => ['Ë'],
        ], $codeMap->getMap($charTree), '', 0, 10, true);
    }
}
