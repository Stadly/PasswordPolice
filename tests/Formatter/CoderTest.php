<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\Coder
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class CoderTest extends TestCase
{
    /**
     * @var CodeMap
     */
    private $codeMap;

    protected function setUp(): void
    {
        $this->codeMap = $this->createMock(CodeMap::class);
        $this->codeMap->method('getMap')->willReturnCallback(
            static function (CharTree $charTree): array {
                $codeMap = [];
                foreach ($charTree->getTreeTrimmedToLength(1) as $char) {
                    $codeMap[$char] = [chr(ord($char)+1)];
                }
                return $codeMap;
            }
        );
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterPath(): void
    {
        $formatter = new Coder($this->codeMap);

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('gPpCbS'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOoBaR'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new Coder($this->codeMap);

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('gPp'),
            CharTree::fromString('CbS'),
            CharTree::fromString('Cb{'),
            CharTree::fromString('234'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
            CharTree::fromString('Baz'),
            CharTree::fromString('123'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new Coder($this->codeMap);

        $next = $this->createMock(Formatter::class);
        $next->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(strrev($string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $formatter->setNext($next);

        self::assertEquals(CharTree::fromArray([
            CharTree::fromString('pPg'),
            CharTree::fromString('SbC'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }
}
