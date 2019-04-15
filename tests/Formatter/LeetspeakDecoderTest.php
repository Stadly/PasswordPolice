<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Formatter\LeetspeakDecoder
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class LeetspeakDecoderTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterPath(): void
    {
        $formatter = new LeetspeakDecoder();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('fOo 1337'),
            CharTree::fromString('fOo 133T'),
            CharTree::fromString('fOo 13E7'),
            CharTree::fromString('fOo 13ET'),
            CharTree::fromString('fOo 1E37'),
            CharTree::fromString('fOo 1E3T'),
            CharTree::fromString('fOo 1EE7'),
            CharTree::fromString('fOo 1EET'),
            CharTree::fromString('fOo I337'),
            CharTree::fromString('fOo I33T'),
            CharTree::fromString('fOo I3E7'),
            CharTree::fromString('fOo I3ET'),
            CharTree::fromString('fOo IE37'),
            CharTree::fromString('fOo IE3T'),
            CharTree::fromString('fOo IEE7'),
            CharTree::fromString('fOo IEET'),
            CharTree::fromString('fOo L337'),
            CharTree::fromString('fOo L33T'),
            CharTree::fromString('fOo L3E7'),
            CharTree::fromString('fOo L3ET'),
            CharTree::fromString('fOo LE37'),
            CharTree::fromString('fOo LE3T'),
            CharTree::fromString('fOo LEE7'),
            CharTree::fromString('fOo LEET'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fOo 1337'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatCharacterTree(): void
    {
        $formatter = new LeetspeakDecoder();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('fXx'),
            CharTree::fromString('fXK'),
            CharTree::fromString('fKx'),
            CharTree::fromString('fKK'),
            CharTree::fromString('B@5'),
            CharTree::fromString('B@S'),
            CharTree::fromString('BA5'),
            CharTree::fromString('BAS'),
            CharTree::fromString('B@4'),
            CharTree::fromString('B@A'),
            CharTree::fromString('BA4'),
            CharTree::fromString('BAA'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fXx'),
            CharTree::fromString('B@5'),
            CharTree::fromString('B@4'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testResultIsUnique(): void
    {
        $formatter = new LeetspeakDecoder();

        self::assertSame(CharTree::fromArray([
            CharTree::fromString('ƒ00'),
            CharTree::fromString('ƒ0O'),
            CharTree::fromString('ƒO0'),
            CharTree::fromString('ƒOO'),
            CharTree::fromString('F00'),
            CharTree::fromString('F0O'),
            CharTree::fromString('FO0'),
            CharTree::fromString('FOO'),
            CharTree::fromString('ƒ°°'),
            CharTree::fromString('ƒ°O'),
            CharTree::fromString('ƒO°'),
            CharTree::fromString('F°°'),
            CharTree::fromString('F°O'),
            CharTree::fromString('FO°'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('ƒ00'),
            CharTree::fromString('ƒ°°'),
        ])));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new LeetspeakDecoder();

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
            CharTree::fromString('xXf'),
            CharTree::fromString('KXf'),
            CharTree::fromString('xKf'),
            CharTree::fromString('KKf'),
            CharTree::fromString('5@B'),
            CharTree::fromString('S@B'),
            CharTree::fromString('5AB'),
            CharTree::fromString('SAB'),
        ]), $formatter->apply(CharTree::fromArray([
            CharTree::fromString('fXx'),
            CharTree::fromString('B@5'),
        ])));
    }
}
