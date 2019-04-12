<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\ValidationError;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\DigitRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class DigitRuleTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new DigitRule(5, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new DigitRule(0, 10);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new DigitRule(5, 10);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new DigitRule(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new DigitRule(10, 5);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new DigitRule(0, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new DigitRule(5, 5);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule1 = new DigitRule(5, 5, 1);
        $rule1->addConstraint(10, 10, 2);

        $rule2 = new DigitRule(10, 10, 2);
        $rule2->addConstraint(5, 5, 1);
        self::assertEquals($rule1, $rule2);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new DigitRule(2, null);

        self::assertTrue($rule->test('FOO bar 059'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new DigitRule(2, null);

        self::assertFalse($rule->test('FOO BAR 0'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new DigitRule(0, 3);

        self::assertTrue($rule->test('FOO bar 059'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new DigitRule(0, 3);

        self::assertFalse($rule->test('foo bar 0597'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new DigitRule(0, 3, 1);

        self::assertTrue($rule->test('foo bar 0597', 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new DigitRule(1, null);

        self::assertNull($rule->validate('1'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new DigitRule(5, null);

        self::assertEquals(
            new ValidationError(
                'The password must contain at least 5 digits.',
                'foo 12',
                $rule,
                1
            ),
            $rule->validate('foo 12')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintCanBeInvalidated(): void
    {
        $rule = new DigitRule(0, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain at most 10 digits.',
                'foo 123 456 123456',
                $rule,
                1
            ),
            $rule->validate('foo 123 456 123456')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithBothMinAndMaxConstraintCanBeInvalidated(): void
    {
        $rule = new DigitRule(5, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain between 5 and 10 digits.',
                'foo 12',
                $rule,
                1
            ),
            $rule->validate('foo 12')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintEqualToZeroCanBeInvalidated(): void
    {
        $rule = new DigitRule(0, 0);

        self::assertEquals(
            new ValidationError(
                'The password cannot contain digits.',
                'foo 12',
                $rule,
                1
            ),
            $rule->validate('foo 12')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintEqualToMaxConstraintCanBeInvalidated(): void
    {
        $rule = new DigitRule(3, 3);

        self::assertEquals(
            new ValidationError(
                'The password must contain exactly 3 digits.',
                'foo 12',
                $rule,
                1
            ),
            $rule->validate('foo 12')
        );
    }
}
