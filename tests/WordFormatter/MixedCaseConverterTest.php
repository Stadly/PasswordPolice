<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordFormatter\MixedCaseConverter
 * @covers ::<protected>
 * @covers ::<private>
 */
final class MixedCaseConverterTest extends TestCase
{
    /**
     * @covers ::apply
     */
    public function testCanFormatWord(): void
    {
        $formatter = new MixedCaseConverter();

        self::assertEquals([
            'f1O2o',
            'F1O2o',
            'f1o2o',
            'F1o2o',
            'f1O2O',
            'F1O2O',
            'f1o2O',
            'F1o2O',
        ], iterator_to_array($formatter->apply(['f1O2o']), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatWords(): void
    {
        $formatter = new MixedCaseConverter();

        self::assertEquals([
            'f1O',
            'F1O',
            'f1o',
            'F1o',
            '2o',
            '2O',
        ], iterator_to_array($formatter->apply(['f1O', '2o']), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanFormatUtf8Characters(): void
    {
        $formatter = new MixedCaseConverter();

        self::assertEquals([
            'Á1æ2Ë',
            'á1æ2Ë',
            'Á1Æ2Ë',
            'á1Æ2Ë',
            'Á1æ2ë',
            'á1æ2ë',
            'Á1Æ2ë',
            'á1Æ2ë',
        ], iterator_to_array($formatter->apply(['Á1æ2Ë']), false), '', 0, 10, true);
    }

    /**
     * @covers ::apply
     */
    public function testCanApplyFormatterChain(): void
    {
        $formatter = new MixedCaseConverter();

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
            'O1f',
            'O1F',
            'o1f',
            'o1F',
            'o2',
            'O2',
        ], iterator_to_array($formatter->apply(['f1O', '2o']), false), '', 0, 10, true);
    }
}
