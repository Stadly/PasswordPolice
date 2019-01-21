<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\FormatterCombiner
 * @covers ::<protected>
 * @covers ::<private>
     * @covers ::__construct
 */
final class FormatterCombinerTest extends TestCase
{
    /**
     * @var MockObject&WordFormatter
     */
    private $wordFormatter1;

    /**
     * @var MockObject&WordFormatter
     */
    private $wordFormatter2;

    protected function setUp(): void
    {
        $this->wordFormatter1 = $this->createMock(WordFormatter::class);
        $this->wordFormatter1->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                    yield strtoupper($word);
                }
            }
        );

        $this->wordFormatter2 = $this->createMock(WordFormatter::class);
        $this->wordFormatter2->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strtolower($word);
                }
            }
        );
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordsWhenIncludingUnformattedAndFilteringUnique(): void
    {
        $formatter = new FormatterCombiner([$this->wordFormatter1, $this->wordFormatter2], true, true);

        self::assertEquals([
            'fOo',
            'BaR',
            'RaB',
            'FOO',
            'bar',
            'BAR',
            'RAB',
            'oOf',
            'OOF',
            'rab',
            'foo',
        ], iterator_to_array($formatter->apply([
            'fOo',
            'BaR',
            'RaB',
            'FOO',
            'bar',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordsWhenIncludingUnformattedAndNotFilteringUnique(): void
    {
        $formatter = new FormatterCombiner([$this->wordFormatter1, $this->wordFormatter2], true, false);

        self::assertEquals([
            'fOo',
            'BaR',
            'RaB',
            'FOO',
            'bar',
            'FOO',
            'BAR',
            'RAB',
            'FOO',
            'BAR',
            'oOf',
            'RaB',
            'BaR',
            'OOF',
            'rab',
            'foo',
            'bar',
            'rab',
            'foo',
            'bar',
        ], iterator_to_array($formatter->apply([
            'fOo',
            'BaR',
            'RaB',
            'FOO',
            'bar',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordsWhenExcludingUnformattedAndFilteringUnique(): void
    {
        $formatter = new FormatterCombiner([$this->wordFormatter1, $this->wordFormatter2], false, true);

        self::assertEquals([
            'FOO',
            'BAR',
            'RAB',
            'oOf',
            'RaB',
            'BaR',
            'OOF',
            'rab',
            'foo',
            'bar',
        ], iterator_to_array($formatter->apply([
            'fOo',
            'BaR',
            'RaB',
            'FOO',
            'bar',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordsWhenExcludingUnformattedAndNotFilteringUnique(): void
    {
        $formatter = new FormatterCombiner([$this->wordFormatter1, $this->wordFormatter2], false, false);

        self::assertEquals([
            'FOO',
            'BAR',
            'RAB',
            'FOO',
            'BAR',
            'oOf',
            'RaB',
            'BaR',
            'OOF',
            'rab',
            'foo',
            'bar',
            'rab',
            'foo',
            'bar',
        ], iterator_to_array($formatter->apply([
            'fOo',
            'BaR',
            'RaB',
            'FOO',
            'bar',
        ]), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new FormatterCombiner([$this->wordFormatter1, $this->wordFormatter2], true, false);
        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        $formatter->setNext($next);

        self::assertEquals([
            'oOf',
            'RaB',
            'BaR',
            'OOF',
            'rab',
            'OOF',
            'RAB',
            'BAR',
            'OOF',
            'RAB',
            'fOo',
            'BaR',
            'RaB',
            'FOO',
            'bar',
            'oof',
            'rab',
            'bar',
            'oof',
            'rab',
        ], iterator_to_array($formatter->apply([
            'fOo',
            'BaR',
            'RaB',
            'FOO',
            'bar',
        ]), false), '', 0, 10, true);
    }
}
