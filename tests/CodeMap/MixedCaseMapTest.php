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
}
