<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\TestException
 * @covers ::<protected>
 * @covers ::<private>
 */
final class TestExceptionTest extends TestCase
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
        $exception = new TestException($this->rule, 'foo');

        // Force generation of code coverage
        $exceptionConstruct = new TestException($this->rule, 'foo');
        self::assertEquals($exception, $exceptionConstruct);
    }

    /**
     * @covers ::getRule
     */
    public function testCanGetRule(): void
    {
        $exception = new TestException($this->rule, 'foo');

        self::assertSame($this->rule, $exception->getRule());
    }
}
