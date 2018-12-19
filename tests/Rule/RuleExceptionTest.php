<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\RuleException
 * @covers ::<protected>
 * @covers ::<private>
 */
final class RuleExceptionTest extends TestCase
{
    /**
     * @var MockObject&RuleInterface
     */
    private $rule;

    protected function setUp(): void
    {
        $this->rule = $this->createMock(RuleInterface::class);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructException(): void
    {
        $exception = new RuleException($this->rule, 1, 'foo');

        // Force generation of code coverage
        $exceptionConstruct = new RuleException($this->rule, 1, 'foo');
        self::assertEquals($exception, $exceptionConstruct);
    }

    /**
     * @covers ::getRule
     */
    public function testCanGetRule(): void
    {
        $exception = new RuleException($this->rule, 1, 'foo');

        self::assertSame($this->rule, $exception->getRule());
    }

    /**
     * @covers ::getWeight
     */
    public function testCanGetWeight(): void
    {
        $exception = new RuleException($this->rule, 1, 'foo');

        self::assertSame(1, $exception->getWeight());
    }
}
