<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Rule;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\RuleException
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class RuleExceptionTest extends TestCase
{
    /**
     * @covers ::getRule
     */
    public function testCanGetRule(): void
    {
        $rule = $this->createMock(Rule::class);

        $exception = new RuleException($rule, 'foo');

        self::assertSame($rule, $exception->getRule());
    }
}
