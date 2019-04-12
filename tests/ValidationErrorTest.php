<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\ValidationError
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ValidationErrorTest extends TestCase
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
     * @covers ::getMessage
     */
    public function testCanGetMessage(): void
    {
        $validationError = new ValidationError('foo', 'bar', $this->rule, 1);

        self::assertSame('foo', $validationError->getMessage());
    }

    /**
     * @covers ::getPassword
     */
    public function testCanGetPassword(): void
    {
        $validationError = new ValidationError('foo', 'bar', $this->rule, 1);

        self::assertSame('bar', $validationError->getPassword());
    }

    /**
     * @covers ::getRule
     */
    public function testCanGetRule(): void
    {
        $validationError = new ValidationError('foo', 'bar', $this->rule, 1);

        self::assertSame($this->rule, $validationError->getRule());
    }

    /**
     * @covers ::getWeight
     */
    public function testCanGetWeight(): void
    {
        $validationError = new ValidationError('foo', 'bar', $this->rule, 1);

        self::assertSame(1, $validationError->getWeight());
    }
}
