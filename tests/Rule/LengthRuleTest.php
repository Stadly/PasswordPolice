<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\ValidationError;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\LengthRule
 * @covers ::<private>
 * @covers ::<protected>
 */
final class LengthRuleTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new LengthRule(5, null);

        // Force generation of code coverage
        $ruleConstruct = new LengthRule(5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new LengthRule(0, 10);

        // Force generation of code coverage
        $ruleConstruct = new LengthRule(0, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new LengthRule(5, 10);

        // Force generation of code coverage
        $ruleConstruct = new LengthRule(5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new LengthRule(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new LengthRule(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new LengthRule(0, null);

        // Force generation of code coverage
        $ruleConstruct = new LengthRule(0, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new LengthRule(5, 5);

        // Force generation of code coverage
        $ruleConstruct = new LengthRule(5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new LengthRule(5, 5, 1);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = new LengthRule(5, 5, 1);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new LengthRule(5, 5, 1);
        $rule->addConstraint(10, 10, 2);

        $ruleConstruct = new LengthRule(10, 10, 2);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new LengthRule(2, null);

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new LengthRule(2, null);

        self::assertFalse($rule->test('f'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new LengthRule(0, 3);

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new LengthRule(0, 3);

        self::assertFalse($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testUtf8IsTreatedAsSingleCharacter(): void
    {
        $rule = new LengthRule(2, 2);

        self::assertTrue($rule->test('áÁ'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new LengthRule(0, 3, 1);

        self::assertTrue($rule->test('foobar', 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new LengthRule(1, null);

        self::assertNull($rule->validate('foo'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new LengthRule(5, null);

        self::assertEquals(
            new ValidationError(
                'The password must contain at least 5 characters.',
                'fo',
                $rule,
                1
            ),
            $rule->validate('fo')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintCanBeInvalidated(): void
    {
        $rule = new LengthRule(0, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain at most 10 characters.',
                'fo bar qwerty',
                $rule,
                1
            ),
            $rule->validate('fo bar qwerty')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithBothMinAndMaxConstraintCanBeInvalidated(): void
    {
        $rule = new LengthRule(5, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain between 5 and 10 characters.',
                'fo',
                $rule,
                1
            ),
            $rule->validate('fo')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintEqualToZeroCanBeInvalidated(): void
    {
        $rule = new LengthRule(0, 0);

        self::assertEquals(
            new ValidationError(
                'The password cannot contain characters.',
                'fo',
                $rule,
                1
            ),
            $rule->validate('fo')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintEqualToMaxConstraintCanBeInvalidated(): void
    {
        $rule = new LengthRule(3, 3);

        self::assertEquals(
            new ValidationError(
                'The password must contain exactly 3 characters.',
                'fo',
                $rule,
                1
            ),
            $rule->validate('fo')
        );
    }
}
