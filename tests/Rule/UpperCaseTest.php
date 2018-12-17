<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\UpperCase
 * @covers ::<protected>
 * @covers ::<private>
 */
final class UpperCaseTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new UpperCase(5, null);

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new UpperCase(0, 10);

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(0, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new UpperCase(5, 10);

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new UpperCase(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new UpperCase(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new UpperCase(0, null);

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(0, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new UpperCase(5, 5);

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new UpperCase(5, 5, 1);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(5, 5, 1);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new UpperCase(5, 5, 1);
        $rule->addConstraint(10, 10, 2);

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(10, 10, 2);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new UpperCase(2, null);

        self::assertTrue($rule->test('foo BAR'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new UpperCase(2, null);

        self::assertFalse($rule->test('foo bar'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new UpperCase(0, 3);

        self::assertTrue($rule->test('foo BAR'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new UpperCase(0, 3);

        self::assertFalse($rule->test('FOO BAR'));
    }

    /**
     * @covers ::test
     */
    public function testUpperCaseUtf8IsCounted(): void
    {
        $rule = new UpperCase(1, null);

        self::assertTrue($rule->test('Á'));
    }

    /**
     * @covers ::test
     */
    public function testLowerCaseUtf8IsNotCounted(): void
    {
        $rule = new UpperCase(1, null);

        self::assertFalse($rule->test('á'));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new UpperCase(1, null);

        $rule->enforce('FOO');

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(1, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new UpperCase(1, null);

        $this->expectException(RuleException::class);

        $rule->enforce('foo');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraint(): void
    {
        $rule = new UpperCase(5, null);

        $this->expectExceptionMessage('There must be at least 5 upper case characters.');

        $rule->enforce('FOo');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraint(): void
    {
        $rule = new UpperCase(0, 10);

        $this->expectExceptionMessage('There must be at most 10 upper case characters.');

        $rule->enforce('FOo BAR QWERTY');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new UpperCase(5, 10);

        $this->expectExceptionMessage('There must be between 5 and 10 upper case characters.');

        $rule->enforce('FOo');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraintEqualToZero(): void
    {
        $rule = new UpperCase(0, 0);

        $this->expectExceptionMessage('There must be no upper case characters.');

        $rule->enforce('FOo');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new UpperCase(3, 3);

        $this->expectExceptionMessage('There must be exactly 3 upper case characters.');

        $rule->enforce('FOo');
    }
}
