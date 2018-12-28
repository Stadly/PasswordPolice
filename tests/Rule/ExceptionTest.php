<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Rule;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\Exception
 * @covers ::<protected>
 * @covers ::<private>
 */
final class ExceptionTest extends TestCase
{
    /**
     * @var MockObject&Rule
     */
    private $rule;

    protected function setUp(): void
    {
        $this->rule = $this->createMock(Rule::class);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructException(): void
    {
        $exception = new Exception($this->rule, 'foo');

        // Force generation of code coverage
        $exceptionConstruct = new Exception($this->rule, 'foo');
        self::assertEquals($exception, $exceptionConstruct);
    }

    /**
     * @covers ::getRule
     */
    public function testCanGetRule(): void
    {
        $exception = new Exception($this->rule, 'foo');

        self::assertSame($this->rule, $exception->getRule());
    }
}
