<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\LowerCase
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LowerCaseTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new LowerCase(5, null);

        // Force generation of code coverage
        $ruleConstruct = new LowerCase(5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new LowerCase(0, 10);

        // Force generation of code coverage
        $ruleConstruct = new LowerCase(0, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new LowerCase(5, 10);

        // Force generation of code coverage
        $ruleConstruct = new LowerCase(5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new LowerCase(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new LowerCase(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructUnconstrainedRule(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new LowerCase(0, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new LowerCase(5, 5);

        // Force generation of code coverage
        $ruleConstruct = new LowerCase(5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::getMin
     */
    public function testCanGetMinConstraint(): void
    {
        $rule = new LowerCase(5, 10);

        self::assertSame(5, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMaxConstraint(): void
    {
        $rule = new LowerCase(5, 10);

        self::assertSame(10, $rule->getMax());
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new LowerCase(2, null);

        self::assertTrue($rule->test('FOO bar'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new LowerCase(2, null);

        self::assertFalse($rule->test('FOO BAR'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new LowerCase(0, 3);

        self::assertTrue($rule->test('FOO bar'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new LowerCase(0, 3);

        self::assertFalse($rule->test('foo bar'));
    }

    /**
     * @covers ::test
     */
    public function testLowerCaseUtf8IsCounted(): void
    {
        $rule = new LowerCase(1, null);

        self::assertTrue($rule->test('á'));
    }

    /**
     * @covers ::test
     */
    public function testUpperCaseUtf8IsNotCounted(): void
    {
        $rule = new LowerCase(1, null);

        self::assertFalse($rule->test('Á'));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new LowerCase(1, null);

        $rule->enforce('foo');

        // Force generation of code coverage
        $ruleConstruct = new LowerCase(1, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new LowerCase(1, null);

        $this->expectException(RuleException::class);

        $rule->enforce('FOO');
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMinConstraint(): void
    {
        $rule = new LowerCase(5, null);

        self::assertSame('There must be at least 5 lower case characters.', $rule->getMessage());
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMaxConstraint(): void
    {
        $rule = new LowerCase(0, 10);

        self::assertSame('There must be at most 10 lower case characters.', $rule->getMessage());
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new LowerCase(5, 10);

        self::assertSame('There must be between 5 and 10 lower case characters.', $rule->getMessage());
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMaxConstraintEqualToZero(): void
    {
        $rule = new LowerCase(0, 0);

        self::assertSame('There must be no lower case characters.', $rule->getMessage());
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new LowerCase(3, 3);

        self::assertSame('There must be exactly 3 lower case characters.', $rule->getMessage());
    }
}
