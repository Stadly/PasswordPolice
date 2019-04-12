<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Rule;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\RuleException
 * @covers ::<private>
 * @covers ::<protected>
 */
final class RuleExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructException(): void
    {
        $rule = $this->createMock(Rule::class);

        $exception = new RuleException($rule, 'foo');

        // Force generation of code coverage
        $exceptionConstruct = new RuleException($rule, 'foo');
        self::assertEquals($exception, $exceptionConstruct);
    }

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
