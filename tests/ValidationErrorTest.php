<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Rule\RuleInterface;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\ValidationError
 * @covers ::<protected>
 * @covers ::<private>
 */
final class ValidationErrorTest extends TestCase
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
        $validationError = new ValidationError($this->rule, 1, 'foo');

        // Force generation of code coverage
        $validationErrorConstruct = new ValidationError($this->rule, 1, 'foo');
        self::assertEquals($validationError, $validationErrorConstruct);
    }

    /**
     * @covers ::getRule
     */
    public function testCanGetRule(): void
    {
        $validationError = new ValidationError($this->rule, 1, 'foo');

        self::assertSame($this->rule, $validationError->getRule());
    }

    /**
     * @covers ::getWeight
     */
    public function testCanGetWeight(): void
    {
        $validationError = new ValidationError($this->rule, 1, 'foo');

        self::assertSame(1, $validationError->getWeight());
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessage(): void
    {
        $validationError = new ValidationError($this->rule, 1, 'foo');

        self::assertSame('foo', $validationError->getMessage());
    }
}
