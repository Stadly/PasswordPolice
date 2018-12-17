<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\Digit
 * @covers ::<protected>
 * @covers ::<private>
 */
final class DigitTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new Digit(5, null);

        // Force generation of code coverage
        $ruleConstruct = new Digit(5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new Digit(0, 10);

        // Force generation of code coverage
        $ruleConstruct = new Digit(0, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new Digit(5, 10);

        // Force generation of code coverage
        $ruleConstruct = new Digit(5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Digit(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Digit(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new Digit(0, null);

        // Force generation of code coverage
        $ruleConstruct = new Digit(0, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new Digit(5, 5);

        // Force generation of code coverage
        $ruleConstruct = new Digit(5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new Digit(5, 5, 1);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = new Digit(5, 5, 1);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new Digit(5, 5, 1);
        $rule->addConstraint(10, 10, 2);

        // Force generation of code coverage
        $ruleConstruct = new Digit(10, 10, 2);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new Digit(2, null);

        self::assertTrue($rule->test('FOO bar 059'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new Digit(2, null);

        self::assertFalse($rule->test('FOO BAR 0'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new Digit(0, 3);

        self::assertTrue($rule->test('FOO bar 059'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new Digit(0, 3);

        self::assertFalse($rule->test('foo bar 0597'));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new Digit(1, null);

        $rule->enforce('1');

        // Force generation of code coverage
        $ruleConstruct = new Digit(1, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new Digit(1, null);

        $this->expectException(RuleException::class);

        $rule->enforce('-');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraint(): void
    {
        $rule = new Digit(5, null);

        $this->expectExceptionMessage('There must be at least 5 digits.');

        $rule->enforce('foo 12');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraint(): void
    {
        $rule = new Digit(0, 10);

        $this->expectExceptionMessage('There must be at most 10 digits.');

        $rule->enforce('foo 123 456 123456');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new Digit(5, 10);

        $this->expectExceptionMessage('There must be between 5 and 10 digits.');

        $rule->enforce('foo 12');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraintEqualToZero(): void
    {
        $rule = new Digit(0, 0);

        $this->expectExceptionMessage('There must be no digits.');

        $rule->enforce('foo 12');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new Digit(3, 3);

        $this->expectExceptionMessage('There must be exactly 3 digits.');

        $rule->enforce('foo 12');
    }
}
