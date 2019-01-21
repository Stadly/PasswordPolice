<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Constraint\CountConstraint
 * @covers ::<protected>
 * @covers ::<private>
 */
final class CountTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithMinConstraint(): void
    {
        $constraint = new CountConstraint(5, null);

        // Force generation of code coverage
        $constraintConstruct = new CountConstraint(5, null);
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithMaxConstraint(): void
    {
        $constraint = new CountConstraint(0, 10);

        // Force generation of code coverage
        $constraintConstruct = new CountConstraint(0, 10);
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithBothMinAndMaxConstraint(): void
    {
        $constraint = new CountConstraint(5, 10);

        // Force generation of code coverage
        $constraintConstruct = new CountConstraint(5, 10);
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $constraint = new CountConstraint(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $constraint = new CountConstraint(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedConstraint(): void
    {
        $constraint = new CountConstraint(0, null);

        // Force generation of code coverage
        $constraintConstruct = new CountConstraint(0, null);
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithMinConstraintEqualToMaxConstraint(): void
    {
        $constraint = new CountConstraint(5, 5);

        // Force generation of code coverage
        $constraintConstruct = new CountConstraint(5, 5);
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithNegativeWeight(): void
    {
        $constraint = new CountConstraint(5, 5, -5);

        // Force generation of code coverage
        $constraintConstruct = new CountConstraint(5, 5, -5);
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::getMin
     */
    public function testCanGetMinConstraint(): void
    {
        $constraint = new CountConstraint(5, 10);

        self::assertSame(5, $constraint->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMaxConstraint(): void
    {
        $constraint = new CountConstraint(5, 10);

        self::assertSame(10, $constraint->getMax());
    }

    /**
     * @covers ::getWeight
     */
    public function testCanGetWeight(): void
    {
        $constraint = new CountConstraint(5, 10, 2);

        self::assertSame(2, $constraint->getWeight());
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $constraint = new CountConstraint(2, null);

        self::assertTrue($constraint->test(3));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $constraint = new CountConstraint(2, null);

        self::assertFalse($constraint->test(0));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $constraint = new CountConstraint(0, 3);

        self::assertTrue($constraint->test(3));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $constraint = new CountConstraint(0, 3);

        self::assertFalse($constraint->test(6));
    }
}
