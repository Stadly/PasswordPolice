<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\ValidationError;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\LowerCaseRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class LowerCaseRuleTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new LowerCaseRule(5, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new LowerCaseRule(0, 10);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new LowerCaseRule(5, 10);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new LowerCaseRule(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new LowerCaseRule(10, 5);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new LowerCaseRule(0, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new LowerCaseRule(5, 5);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule1 = new LowerCaseRule(5, 5, 1);
        $rule1->addConstraint(10, 10, 2);

        $rule2 = new LowerCaseRule(10, 10, 2);
        $rule2->addConstraint(5, 5, 1);
        self::assertEquals($rule1, $rule2);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new LowerCaseRule(2, null);

        self::assertTrue($rule->test('FOO bar'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new LowerCaseRule(2, null);

        self::assertFalse($rule->test('FOO BAR'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new LowerCaseRule(0, 3);

        self::assertTrue($rule->test('FOO bar'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new LowerCaseRule(0, 3);

        self::assertFalse($rule->test('foo bar'));
    }

    /**
     * @covers ::test
     */
    public function testLowerCaseUtf8IsCounted(): void
    {
        $rule = new LowerCaseRule(1, null);

        self::assertTrue($rule->test('á'));
    }

    /**
     * @covers ::test
     */
    public function testUpperCaseUtf8IsNotCounted(): void
    {
        $rule = new LowerCaseRule(1, null);

        self::assertFalse($rule->test('Á'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new LowerCaseRule(0, 3, 1);

        self::assertTrue($rule->test('foo bar', 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new LowerCaseRule(1, null);

        self::assertNull($rule->validate('foo'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new LowerCaseRule(5, null);

        self::assertEquals(
            new ValidationError(
                'The password must contain at least 5 lower case letters.',
                'Foo',
                $rule,
                1
            ),
            $rule->validate('Foo')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintCanBeInvalidated(): void
    {
        $rule = new LowerCaseRule(0, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain at most 10 lower case letters.',
                'Foo bar qwerty test',
                $rule,
                1
            ),
            $rule->validate('Foo bar qwerty test')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithBothMinAndMaxConstraintCanBeInvalidated(): void
    {
        $rule = new LowerCaseRule(5, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain between 5 and 10 lower case letters.',
                'Foo',
                $rule,
                1
            ),
            $rule->validate('Foo')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintEqualToZeroCanBeInvalidated(): void
    {
        $rule = new LowerCaseRule(0, 0);

        self::assertEquals(
            new ValidationError(
                'The password cannot contain lower case letters.',
                'Foo',
                $rule,
                1
            ),
            $rule->validate('Foo')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintEqualToMaxConstraintCanBeInvalidated(): void
    {
        $rule = new LowerCaseRule(3, 3);

        self::assertEquals(
            new ValidationError(
                'The password must contain exactly 3 lower case letters.',
                'Foo',
                $rule,
                1
            ),
            $rule->validate('Foo')
        );
    }
}
