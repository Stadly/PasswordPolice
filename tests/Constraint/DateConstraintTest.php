<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Constraint\DateConstraint
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class DateConstraintTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithMinConstraint(): void
    {
        $constraint = new DateConstraint(new DateTime('2001-02-03'), null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithMaxConstraint(): void
    {
        $constraint = new DateConstraint(null, new DateTime('2002-03-04'));
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithBothMinAndMaxConstraint(): void
    {
        $constraint = new DateConstraint(new DateTime('2001-02-03'), new DateTime('2002-03-04'));
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $constraint = new DateConstraint(new DateTime('2002-03-04'), new DateTime('2001-02-03'));
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructUnconstrainedConstraint(): void
    {
        $constraint = new DateConstraint(null, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithMinConstraintEqualToMaxConstraint(): void
    {
        $constraint = new DateConstraint(new DateTime('2001-02-03'), new DateTime('2001-02-03'));
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructConstraintWithNegativeWeight(): void
    {
        $constraint = new DateConstraint(new DateTime('2001-02-03'), new DateTime('2001-02-03'), -5);
    }

    /**
     * @covers ::getMin
     */
    public function testCanGetMinConstraint(): void
    {
        $min = new DateTime('2001-02-03');
        $max = new DateTime('2002-03-04');
        $constraint = new DateConstraint($min, $max);

        self::assertSame($min, $constraint->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMaxConstraint(): void
    {
        $min = new DateTime('2001-02-03');
        $max = new DateTime('2002-03-04');
        $constraint = new DateConstraint($min, $max);

        self::assertSame($max, $constraint->getMax());
    }

    /**
     * @covers ::getWeight
     */
    public function testCanGetWeight(): void
    {
        $constraint = new DateConstraint(new DateTime('2001-02-03'), new DateTime('2002-03-04'), 2);

        self::assertSame(2, $constraint->getWeight());
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $constraint = new DateConstraint(new DateTime('2001-02-03'), null);

        self::assertTrue($constraint->test(new DateTime('2001-02-04')));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $constraint = new DateConstraint(new DateTime('2001-02-03'), null);

        self::assertFalse($constraint->test(new DateTime('2001-02-02')));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $constraint = new DateConstraint(null, new DateTime('2002-03-04'));

        self::assertTrue($constraint->test(new DateTime('2002-03-03')));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $constraint = new DateConstraint(null, new DateTime('2002-03-04'));

        self::assertFalse($constraint->test(new DateTime('2002-03-05')));
    }
}
