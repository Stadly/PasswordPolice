<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Constraint\PositionConstraint
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class PositionConstraintTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithFirstConstraint(): void
    {
        new PositionConstraint(5, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithCountConstraint(): void
    {
        new PositionConstraint(0, 10);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithBothFirstAndCountConstraint(): void
    {
        new PositionConstraint(5, 10);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithNegativeFirstConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PositionConstraint(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithCountConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PositionConstraint(0, 0);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithNegativeCountConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PositionConstraint(0, -10);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructUnconstrainedConstraint(): void
    {
        new PositionConstraint(0, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithFirstConstraintEqualToCountConstraint(): void
    {
        new PositionConstraint(5, 5);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithNegativeWeight(): void
    {
        new PositionConstraint(5, 5, -5);
    }

    /**
     * @covers ::getFirst
     */
    public function testCanGetFirstConstraint(): void
    {
        $constraint = new PositionConstraint(5, 10);

        self::assertSame(5, $constraint->getFirst());
    }

    /**
     * @covers ::getCount
     */
    public function testCanGetCountConstraint(): void
    {
        $constraint = new PositionConstraint(5, 10);

        self::assertSame(10, $constraint->getCount());
    }

    /**
     * @covers ::getWeight
     */
    public function testCanGetWeight(): void
    {
        $constraint = new PositionConstraint(5, 10, 2);

        self::assertSame(2, $constraint->getWeight());
    }

    /**
     * @covers ::test
     */
    public function testFirstConstraintCanBeSatisfied(): void
    {
        $constraint = new PositionConstraint(2, null);

        self::assertTrue($constraint->test(3));
    }

    /**
     * @covers ::test
     */
    public function testFirstConstraintCanBeUnsatisfied(): void
    {
        $constraint = new PositionConstraint(2, null);

        self::assertFalse($constraint->test(0));
    }

    /**
     * @covers ::test
     */
    public function testCountConstraintCanBeSatisfied(): void
    {
        $constraint = new PositionConstraint(3, 3);

        self::assertTrue($constraint->test(3));
    }

    /**
     * @covers ::test
     */
    public function testCountConstraintCanBeUnsatisfied(): void
    {
        $constraint = new PositionConstraint(3, 3);

        self::assertFalse($constraint->test(6));
    }
}
