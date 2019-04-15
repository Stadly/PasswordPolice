<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\Combiner
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class CombinerTest extends TestCase
{
    /**
     * @var Formatter
     */
    private $formatter1;

    /**
     * @var Formatter
     */
    private $formatter2;

    protected function setUp(): void
    {
        $this->formatter1 = $this->createMock(Formatter::class);
        $this->formatter1->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(strrev($string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $this->formatter2 = $this->createMock(Formatter::class);
        $this->formatter2->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(strtolower($string));
                    $charTrees[] = CharTree::fromString(strtoupper($string));
                }
                return CharTree::fromArray($charTrees);
            }
        );
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new Combiner([$this->formatter1, $this->formatter2], false);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('oOf'),
            CharTree::fromString('RaB'),
            CharTree::fromString('foo'),
            CharTree::fromString('bar'),
            CharTree::fromString('FOO'),
            CharTree::fromString('BAR'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTreeAndIncludeUnformatted(): void
    {
        $formatter = new Combiner([$this->formatter1, $this->formatter2], true);

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
            CharTree::fromString('oOf'),
            CharTree::fromString('RaB'),
            CharTree::fromString('foo'),
            CharTree::fromString('bar'),
            CharTree::fromString('FOO'),
            CharTree::fromString('BAR'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new Combiner([$this->formatter1, $this->formatter2], false);

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
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
            CharTree::fromString('oof'),
            CharTree::fromString('rab'),
            CharTree::fromString('OOF'),
            CharTree::fromString('RAB'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo'),
            CharTree::fromString('BaR'),
        ])));
    }
}
