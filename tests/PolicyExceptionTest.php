<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Rule\Digit;
use Stadly\PasswordPolice\Rule\RuleException;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\PolicyException
 * @covers ::<protected>
 * @covers ::<private>
 */
final class PolicyExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructException(): void
    {
        $exception = new PolicyException(new Policy(), [new RuleException(new Digit(2), 'foo')]);

        // Force generation of code coverage
        $exceptionConstruct = new PolicyException(new Policy(), [new RuleException(new Digit(2), 'foo')]);
        self::assertEquals($exception, $exceptionConstruct);
    }

    /**
     * @covers ::getPolicy
     */
    public function testCanGetPolicy(): void
    {
        $policy = new Policy();
        $exception = new PolicyException($policy, [new RuleException(new Digit(2), 'foo')]);

        self::assertSame($policy, $exception->getPolicy());
    }

    /**
     * @covers ::getRuleExceptions
     */
    public function testCanGetRuleExceptions(): void
    {
        $ruleExceptions = [new RuleException(new Digit(2), 'foo')];
        $exception = new PolicyException(new Policy(), $ruleExceptions);

        self::assertSame($ruleExceptions, $exception->getRuleExceptions());
    }
}
