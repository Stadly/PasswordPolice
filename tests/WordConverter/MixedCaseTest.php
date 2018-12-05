<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\WordConverter\MixedCase
 * @covers ::<protected>
 * @covers ::<private>
 */
final class MixedCaseTest extends TestCase
{
    /**
     * @covers ::convert
     */
    public function testCanConvertWord(): void
    {
        $converter = new MixedCase();

        self::assertEquals([
            'f1O2o',
            'F1O2o',
            'f1o2o',
            'F1o2o',
            'f1O2O',
            'F1O2O',
            'f1o2O',
            'F1o2O',
        ], iterator_to_array($converter->convert('f1O2o')), '', 0, 10, true);
    }

    /**
     * @covers ::convert
     */
    public function testCanConvertUtf8Characters(): void
    {
        $converter = new MixedCase();

        self::assertEquals([
            'Á1æ2Ë',
            'á1æ2Ë',
            'Á1Æ2Ë',
            'á1Æ2Ë',
            'Á1æ2ë',
            'á1æ2ë',
            'Á1Æ2ë',
            'á1Æ2ë',
        ], iterator_to_array($converter->convert('Á1æ2Ë')), '', 0, 10, true);
    }
}
