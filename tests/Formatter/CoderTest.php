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
        $this->codeMap->method('getLengths')->willReturn([1]);
        $this->codeMap->method('code')->willReturnCallback(
            static function (string $string): array {
                $char = mb_substr($string, 0, 1);
                return [chr(ord($char) + 1)];
            }
        );
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatEmptyCharacterTree(): void
    {
        $formatter = new CoderClass($this->codeMap);

        self::assertSame(CharTree::fromArray([
        ]), $formatter->apply(CharTree::fromArray([
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatEmptyStringCharacterTree(): void
    {
        $formatter = new CoderClass($this->codeMap);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString(''),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString(''),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterPath(): void
    {
        $formatter = new CoderClass($this->codeMap);

        self::assertSame(CharTree::fromArray([
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
        $formatter = new CoderClass($this->codeMap);

        self::assertSame(CharTree::fromArray([
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
        $formatter = new CoderClass($this->codeMap);

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

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('pPg'),
            CharTree::fromString('SbC'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }
}
