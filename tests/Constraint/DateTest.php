<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Constraint;

use DateInterval;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Constraint\Date
 * @covers ::<protected>
 * @covers ::<private>
 */
final class DateTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithMinConstraint(): void
    {
        $constraint = new Date(new DateInterval('P5D'), null);

        // Force generation of code coverage
        $constraintConstruct = new Date(new DateInterval('P5D'), null);
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithMaxConstraint(): void
    {
        $constraint = new Date(new DateInterval('PT0S'), new DateInterval('P10D'));

        // Force generation of code coverage
        $constraintConstruct = new Date(new DateInterval('PT0S'), new DateInterval('P10D'));
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithBothMinAndMaxConstraint(): void
    {
        $constraint = new Date(new DateInterval('P5D'), new DateInterval('P10D'));

        // Force generation of code coverage
        $constraintConstruct = new Date(new DateInterval('P5D'), new DateInterval('P10D'));
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $constraint = new Date(DateInterval::createFromDateString('-10 days'), null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructConstraintWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $constraint = new Date(new DateInterval('P10D'), new DateInterval('P5D'));
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedConstraint(): void
    {
        $constraint = new Date(new DateInterval('PT0S'), null);

        // Force generation of code coverage
        $constraintConstruct = new Date(new DateInterval('PT0S'), null);
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithMinConstraintEqualToMaxConstraint(): void
    {
        $constraint = new Date(new DateInterval('P5D'), new DateInterval('P5D'));

        // Force generation of code coverage
        $constraintConstruct = new Date(new DateInterval('P5D'), new DateInterval('P5D'));
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructConstraintWithNegativeWeight(): void
    {
        $constraint = new Date(new DateInterval('P5D'), new DateInterval('P5D'), -5);

        // Force generation of code coverage
        $constraintConstruct = new Date(new DateInterval('P5D'), new DateInterval('P5D'), -5);
        self::assertEquals($constraint, $constraintConstruct);
    }

    /**
     * @covers ::getMin
     */
    public function testCanGetMinConstraint(): void
    {
        $min = new DateInterval('P5D');
        $max = new DateInterval('P10D');
        $rule = new Date($min, $max);

        self::assertSame($min, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMaxConstraint(): void
    {
        $min = new DateInterval('P5D');
        $max = new DateInterval('P10D');
        $rule = new Date($min, $max);

        self::assertSame($max, $rule->getMax());
    }

    /**
     * @covers ::getWeight
     */
    public function testCanGetWeight(): void
    {
        $constraint = new Date(new DateInterval('P5D'), new DateInterval('P10D'), 2);

        self::assertSame(2, $constraint->getWeight());
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $constraint = new Date(new DateInterval('P5D'), null);

        self::assertTrue($constraint->test(new DateTime('-7 days')));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $constraint = new Date(new DateInterval('P5D'), null);

        self::assertFalse($constraint->test(new DateTime('-1 days')));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $constraint = new Date(new DateInterval('PT0S'), new DateInterval('P10D'));

        self::assertTrue($constraint->test(new DateTime('-9 days')));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $constraint = new Date(new DateInterval('PT0S'), new DateInterval('P10D'));

        self::assertFalse($constraint->test(new DateTime('-15 days')));
    }

    /**
     * @covers ::test
     */
    public function testConstraintIsUnsatisfiedWhenComparingFutureDate(): void
    {
        $constraint = new Date(new DateInterval('PT0S'), null);

        self::assertFalse($constraint->test(new DateTime('+1 day')));
    }
}
