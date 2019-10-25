<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\CharTree
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class CharTreeTest extends TestCase
{
    /**
     * @covers ::fromString
     */
    public function testCanConstructCharTreeFromEmptyString(): void
    {
        $charTree = CharTree::fromString('');

        self::assertSame('', $charTree->getRoot());
        self::assertEquals([
        ], $charTree->getBranches(), '', 0, 10, true);
    }

    /**
     * @covers ::fromString
     */
    public function testCanConstructCharTreeFromSingleCharacterString(): void
    {
        $charTree = CharTree::fromString('f');

        self::assertSame('f', $charTree->getRoot());
        self::assertEquals([
        ], $charTree->getBranches(), '', 0, 10, true);
    }

    /**
     * @covers ::fromString
     */
    public function testCanConstructCharTreeFromMultipleCharactersString(): void
    {
        $charTree = CharTree::fromString('fo');

        self::assertSame(CharTree::fromString('f', [
            CharTree::fromString('o'),
        ]), $charTree);
    }

    /**
     * @covers ::fromString
     */
    public function testCanConstructCharTreeFromEmptyStringAndEmptyNextCharTree(): void
    {
        $charTree = CharTree::fromString('', [
            CharTree::fromString(''),
        ]);

        self::assertSame(CharTree::fromString(''), $charTree);
    }

    /**
     * @covers ::fromString
     */
    public function testCanConstructCharTreeFromEmptyStringAndSingleNextCharTree(): void
    {
        $charTree = CharTree::fromString('', [
            CharTree::fromString('a'),
        ]);

        self::assertSame(CharTree::fromString('a'), $charTree);
    }

    /**
     * @covers ::fromString
     */
    public function testCanConstructCharTreeFromEmptyStringAndDuplicateNextCharTree(): void
    {
        $charTree = CharTree::fromString('', [
            CharTree::fromString('ab'),
            CharTree::fromString('ab'),
        ]);

        self::assertSame(CharTree::fromString('a', [
            CharTree::fromString('b'),
        ]), $charTree);
    }

    /**
     * @covers ::fromString
     */
    public function testCanConstructCharTreeFromStringWithEmptyNextCharTree(): void
    {
        $charTree = CharTree::fromString('a', [
            CharTree::fromString(''),
        ]);

        self::assertSame(CharTree::fromString('a'), $charTree);
    }

    /**
     * @covers ::fromString
     */
    public function testCanConstructCharTreeFromStringWithMultipleNextCharTreesHavingEmptyRoot(): void
    {
        $charTree = CharTree::fromString('a', [
            CharTree::fromString(''),
            CharTree::fromString('', [
                CharTree::fromString('b'),
                CharTree::fromString('c'),
            ]),
        ]);

        self::assertSame(CharTree::fromString('a', [
            CharTree::fromString(''),
            CharTree::fromString('b'),
            CharTree::fromString('c'),
        ]), $charTree);
    }

    /**
     * @covers ::fromString
     */
    public function testCanConstructCharTreeWithMultipleNextCharTreesHavingTheSameRoot(): void
    {
        $charTree = CharTree::fromString('a', [
            CharTree::fromString('b'),
            CharTree::fromString('bc'),
            CharTree::fromString('b'),
            CharTree::fromString('bd'),
            CharTree::fromString('b'),
        ]);

        self::assertSame(CharTree::fromString('a', [
            CharTree::fromString('b', [
                CharTree::fromString(''),
                CharTree::fromString('c'),
                CharTree::fromString('d'),
            ]),
        ]), $charTree);
    }

    /**
     * @covers ::fromNothing
     * @runInSeparateProcess Code coverage for empty character tree memoized in other tests.
     */
    public function testCanConstructCharTreeFromNothing(): void
    {
        $charTree = CharTree::fromNothing();

        self::assertNull($charTree->getRoot());
        self::assertEquals([
        ], $charTree->getBranches(), '', 0, 10, true);
    }

    /**
     * @covers ::fromArray
     */
    public function testCanConstructCharTreeFromEmptyArray(): void
    {
        $charTree = CharTree::fromArray([]);

        self::assertSame(CharTree::fromNothing(), $charTree);
    }

    /**
     * @covers ::fromArray
     */
    public function testCanConstructCharTreeFromArray(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('ab'),
            CharTree::fromString('ac'),
        ]);

        self::assertSame(CharTree::fromString('a', [
            CharTree::fromString('b'),
            CharTree::fromString('c'),
        ]), $charTree);
    }

    /**
     * @covers ::fromArray
     */
    public function testCanConstructCharTreeFromArrayWithEmptyCharTree(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromNothing(),
        ]);

        self::assertSame(CharTree::fromNothing(), $charTree);
    }

    /**
     * @covers ::fromArray
     */
    public function testCanConstructCharTreeFromArrayWithEmptyStringCharTree(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString(''),
        ]);

        self::assertSame(CharTree::fromString(''), $charTree);
    }

    /**
     * @covers ::getRoot
     */
    public function testCanGetRoot(): void
    {
        $charTree = CharTree::fromString('a');

        self::assertSame('a', $charTree->getRoot());
    }

    /**
     * @covers ::getBranches
     */
    public function testCanGetBranches(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('ab'),
            CharTree::fromString('ac'),
        ]);

        self::assertEquals([
            'b' => CharTree::fromString('b'),
            'c' => CharTree::fromString('c'),
        ], $charTree->getBranches(), '', 0, 10, true);
    }

    /**
     * @covers ::contains
     */
    public function testCharTreeContainsPrefix(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]);

        self::assertTrue($charTree->contains('ac'));
    }

    /**
     * @covers ::contains
     */
    public function testCharTreeContainsInfix(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]);

        self::assertTrue($charTree->contains('oba'));
    }

    /**
     * @covers ::contains
     */
    public function testCharTreeContainsSuffix(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]);

        self::assertTrue($charTree->contains('bc'));
    }

    /**
     * @covers ::contains
     */
    public function testCharTreeContainsWholeString(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]);

        self::assertTrue($charTree->contains('acd'));
    }

    /**
     * @covers ::contains
     */
    public function testCharTreeDoesNotContainOtherString(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]);

        self::assertFalse($charTree->contains('baz'));
    }

    /**
     * @covers ::contains
     */
    public function testContainsCanConsiderCase(): void
    {
        $charTree = CharTree::fromString('foobar');

        self::assertFalse($charTree->contains('OBa', /*ignoreCase*/false));
    }

    /**
     * @covers ::contains
     */
    public function testContainsCanIgnoreCase(): void
    {
        $charTree = CharTree::fromString('foobar');

        self::assertTrue($charTree->contains('OBa', /*ignoreCase*/true));
    }

    /**
     * @covers ::startsWith
     */
    public function testCharTreeStartsWithPrefix(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('t'),
        ]);

        self::assertTrue($charTree->startsWith('ac'));
    }

    /**
     * @covers ::startsWith
     */
    public function testCharTreeDoesNotStartWithInfix(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('t'),
        ]);

        self::assertFalse($charTree->startsWith('oba'));
    }

    /**
     * @covers ::startsWith
     */
    public function testCharTreeDoesNotStartWithSuffix(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('t'),
        ]);

        self::assertFalse($charTree->startsWith('bc'));
    }

    /**
     * @covers ::startsWith
     */
    public function testCharTreeStartsWithWholeString(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('t'),
        ]);

        self::assertTrue($charTree->startsWith('acd'));
    }

    /**
     * @covers ::startsWith
     */
    public function testCharTreeDoesNotStartWithOtherString(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('t'),
        ]);

        self::assertFalse($charTree->startsWith('baz'));
    }

    /**
     * @covers ::startsWith
     */
    public function testStartsWithCanConsiderCase(): void
    {
        $charTree = CharTree::fromString('foobar');

        self::assertFalse($charTree->startsWith('fOO', /*ignoreCase*/false));
    }

    /**
     * @covers ::startsWith
     */
    public function testStartsWithCanIgnoreCase(): void
    {
        $charTree = CharTree::fromString('foobar');

        self::assertTrue($charTree->startsWith('fOO', /*ignoreCase*/true));
    }

    /**
     * @covers ::getIterator
     */
    public function testCanIterateOverAllStrings(): void
    {
        $charTree = CharTree::fromArray([
            CharTree::fromString('a'),
            CharTree::fromString('ab'),
            CharTree::fromString('abc'),
            CharTree::fromString('acd'),
            CharTree::fromString('foobar'),
            CharTree::fromString('k'),
        ]);

        self::assertEquals([
            'a',
            'ab',
            'abc',
            'acd',
            'foobar',
            'k',
        ], iterator_to_array($charTree->getIterator(), false), '', 0, 10, true);
    }
}
