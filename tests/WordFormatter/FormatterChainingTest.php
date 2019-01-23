<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\FormatterChaining
 * @covers ::<protected>
 * @covers ::<private>
 */
final class FormatterChainingTest extends TestCase
{
    /**
     * @var MockObject&WordFormatter
     */
    private $wordFormatter;

    protected function setUp(): void
    {
        $this->wordFormatter = $this->createMock(WordFormatter::class);
    }

    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanSetAndGetNext(): void
    {
        /**
         * @var MockObject&FormatterChaining
         */
        $formatter = $this->getMockForTrait(FormatterChaining::class);

        $formatter->setNext($this->wordFormatter);
        self::assertSame($this->wordFormatter, $formatter->getNext());
    }

    /**
     * @covers ::setNext
     * @covers ::getNext
     */
    public function testCanGetWhenNoNextIsSet(): void
    {
        /**
         * @var MockObject&FormatterChaining
         */
        $formatter = $this->getMockForTrait(FormatterChaining::class);

        $formatter->setNext($this->wordFormatter);
        $formatter->setNext(null);

        self::assertNull($formatter->getNext());
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatter(): void
    {
        /**
         * @var MockObject&FormatterChaining
         */
        $formatter = $this->getMockForTrait(FormatterChaining::class);
        $formatter->method('applyCurrent')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        self::assertSame(['cba', 'fed'], iterator_to_array($formatter->apply(['abc', 'def']), false));
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        /**
         * @var MockObject&FormatterChaining
         */
        $formatter = $this->getMockForTrait(FormatterChaining::class);
        $formatter->method('applyCurrent')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strrev($word);
                }
            }
        );

        $next = $this->createMock(WordFormatter::class);
        $next->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield strtoupper($word);
                }
            }
        );

        $formatter->setNext($next);

        self::assertSame(['CBA', 'FED'], iterator_to_array($formatter->apply(['abc', 'def']), false));
    }
}
