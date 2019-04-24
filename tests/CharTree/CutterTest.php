<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CharTree;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CharTree\Cutter
 * @covers ::<private>
 * @covers ::<protected>
 */
final class CutterTest extends TestCase
{
    /**
     * @covers ::cut
     */
    public function testCannotCutEmptyCharTreeOnNegativePosition(): void
    {
        $cutter = new Cutter();

        $this->expectException(InvalidArgumentException::class);

        $cutter->cut(CharTree::fromArray([
        ]), -5);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutEmptyCharTreeOnZeroPosition(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
        ], $cutter->cut(CharTree::fromArray([
        ]), 0), '', 0, 10, true);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutEmptyCharTreeOnPositivePosition(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
        ], $cutter->cut(CharTree::fromArray([
        ]), 3), '', 0, 10, true);
    }

    /**
     * @covers ::cut
     */
    public function testCannotCutEmptyStringCharTreeOnNegativePosition(): void
    {
        $cutter = new Cutter();

        $this->expectException(InvalidArgumentException::class);

        $cutter->cut(CharTree::fromArray([
            CharTree::fromString(''),
        ]), -5);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutEmptyStringCharTreeOnZeroPosition(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
            ['', CharTree::fromNothing()],
        ], $cutter->cut(CharTree::fromArray([
            CharTree::fromString(''),
        ]), 0), '', 0, 10, true);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutEmptyStringCharTreeOnPositivePosition(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
        ], $cutter->cut(CharTree::fromArray([
            CharTree::fromString(''),
        ]), 3), '', 0, 10, true);
    }

    /**
     * @covers ::cut
     */
    public function testCannotCutCharPathOnNegativePosition(): void
    {
        $cutter = new Cutter();

        $this->expectException(InvalidArgumentException::class);

        $cutter->cut(CharTree::fromArray([
            CharTree::fromString('foobar'),
        ]), -5);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutCharPathOnZeroPosition(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
            ['', CharTree::fromString('foobar')],
        ], $cutter->cut(CharTree::fromArray([
            CharTree::fromString('foobar'),
        ]), 0), '', 0, 10, true);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutCharPathOnPositionSmallerThanLength(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
            ['foo', CharTree::fromString('bar')],
        ], $cutter->cut(CharTree::fromArray([
            CharTree::fromString('foobar'),
        ]), 3), '', 0, 10, true);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutCharPathOnPositionEqualToLength(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
            ['foobar', CharTree::fromNothing()],
        ], $cutter->cut(CharTree::fromArray([
            CharTree::fromString('foobar'),
        ]), 6), '', 0, 10, true);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutCharPathOnPositionGreaterThanLength(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
        ], $cutter->cut(CharTree::fromArray([
            CharTree::fromString('foobar'),
        ]), 9), '', 0, 10, true);
    }

    /**
     * @covers ::cut
     */
    public function testCannotCutCharTreeOnNegativePosition(): void
    {
        $cutter = new Cutter();

        $this->expectException(InvalidArgumentException::class);

        $cutter->cut(CharTree::fromArray([
            CharTree::fromString(''),
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('ac'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]), -5);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutCharTreeOnZeroPosition(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
            ['', CharTree::fromArray([
                CharTree::fromString(''),
                CharTree::fromString('a'),
                CharTree::fromString('ab'),
                CharTree::fromString('abc'),
                CharTree::fromString('ac'),
                CharTree::fromString('foobar'),
                CharTree::fromString('k'),
            ])],
        ], $cutter->cut(CharTree::fromArray([
            CharTree::fromString(''),
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('ac'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]), 0), '', 0, 10, true);
    }

    /**
     * @covers ::cut
     */
    public function testCanCutCharTreeOnPositivePosition(): void
    {
        $cutter = new Cutter();

        self::assertEquals([
            ['ab', CharTree::fromArray([
                CharTree::fromString(''),
                CharTree::fromString('c'),
                CharTree::fromString('cd'),
            ])],
            ['ac', CharTree::fromArray([
            ])],
            ['fo', CharTree::fromArray([
                CharTree::fromString('obar'),
            ])],
        ], $cutter->cut(CharTree::fromArray([
            CharTree::fromString(''),
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('abcd'),
            CharTree::fromString('ac'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]), 2), '', 0, 10, true);
    }
}
