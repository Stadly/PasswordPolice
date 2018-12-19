<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Rule\RuleException;
use Stadly\PasswordPolice\Rule\RuleInterface;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\PolicyException
 * @covers ::<protected>
 * @covers ::<private>
 */
final class PolicyExceptionTest extends TestCase
{
    /**
     * @var RuleException
     */
    private $ruleException;

    protected function setUp(): void
    {
        $this->ruleException = new RuleException($this->createMock(RuleInterface::class), 1, 'foo');
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructException(): void
    {
        $exception = new PolicyException(new Policy(), [$this->ruleException]);

        // Force generation of code coverage
        $exceptionConstruct = new PolicyException(new Policy(), [$this->ruleException]);
        self::assertEquals($exception, $exceptionConstruct);
    }

    /**
     * @covers ::getPolicy
     */
    public function testCanGetPolicy(): void
    {
        $policy = new Policy();
        $exception = new PolicyException($policy, [$this->ruleException]);

        self::assertSame($policy, $exception->getPolicy());
    }

    /**
     * @covers ::getRuleExceptions
     */
    public function testCanGetRuleExceptions(): void
    {
        $exception = new PolicyException(new Policy(), [$this->ruleException]);

        self::assertSame([$this->ruleException], $exception->getRuleExceptions());
    }
}
