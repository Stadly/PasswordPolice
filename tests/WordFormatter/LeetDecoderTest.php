<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\LeetDecoder
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LeetDecoderTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructFormatter(): void
    {
        $formatter = new LeetDecoder();

        // Force generation of code coverage
        $formatterConstruct = new LeetDecoder();
        self::assertEquals($formatter, $formatterConstruct);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithoutLeetspeak(): void
    {
        $formatter = new LeetDecoder();

        self::assertSame(['fOoBaR'], iterator_to_array($formatter->apply(['fOoBaR']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordsWithoutLeetspeak(): void
    {
        $formatter = new LeetDecoder();

        self::assertSame(['fOo', 'BaR'], iterator_to_array($formatter->apply(['fOo', 'BaR']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordWithLeetspeak(): void
    {
        $formatter = new LeetDecoder();

        self::assertContains('LEET SPEAK', iterator_to_array($formatter->apply(['1337 5P34K']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWordsWithLeetspeak(): void
    {
        $formatter = new LeetDecoder();

        self::assertContains('LEET', iterator_to_array($formatter->apply(['1337', '5P34K']), false));
        self::assertContains('SPEAK', iterator_to_array($formatter->apply(['1337', '5P34K']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new LeetDecoder();

        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        $formatter->setNext($next);

        self::assertContains('TEEL', iterator_to_array($formatter->apply(['1337', '5P34K']), false));
        self::assertContains('KAEPS', iterator_to_array($formatter->apply(['1337', '5P34K']), false));
    }
}
