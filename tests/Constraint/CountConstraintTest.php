<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Constraint\CountConstraint
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class CountConstraintTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithMinConstraint(): void
    {
        new CountConstraint(5, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithMaxConstraint(): void
    {
        new CountConstraint(0, 10);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithBothMinAndMaxConstraint(): void
    {
        new CountConstraint(5, 10);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CountConstraint(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CountConstraint(10, 5);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructUnconstrainedConstraint(): void
    {
        new CountConstraint(0, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithMinConstraintEqualToMaxConstraint(): void
    {
        new CountConstraint(5, 5);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithNegativeWeight(): void
    {
        new CountConstraint(5, 5, -5);
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
