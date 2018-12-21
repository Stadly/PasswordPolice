<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\Symbol
 * @covers ::<protected>
 * @covers ::<private>
 */
final class SymbolTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new Symbol('$%&@!', 5, null);

        // Force generation of code coverage
        $ruleConstruct = new Symbol('$%&@!', 5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new Symbol('$%&@!', 0, 10);

        // Force generation of code coverage
        $ruleConstruct = new Symbol('$%&@!', 0, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new Symbol('$%&@!', 5, 10);

        // Force generation of code coverage
        $ruleConstruct = new Symbol('$%&@!', 5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Symbol('$%&@!', -10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Symbol('$%&@!', 10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new Symbol('$%&@!', 0, null);

        // Force generation of code coverage
        $ruleConstruct = new Symbol('$%&@!', 0, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new Symbol('$%&@!', 5, 5);

        // Force generation of code coverage
        $ruleConstruct = new Symbol('$%&@!', 5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNoCharacters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Symbol('');
    }

    /**
     * @covers ::getCharacters
     */
    public function testCanGetCharacters(): void
    {
        $rule = new Symbol('$%&@!');

        self::assertSame('$%&@!', $rule->getCharacters());
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new Symbol('$%&@!', 5, 5, 1);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = new Symbol('$%&@!', 5, 5, 1);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new Symbol('$%&@!', 5, 5, 1);
        $rule->addConstraint(10, 10, 2);

        $ruleConstruct = new Symbol('$%&@!', 10, 10, 2);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new Symbol('$%&@!', 2, null);

        self::assertTrue($rule->test('FOO bar $$@'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new Symbol('$%&@!', 2, null);

        self::assertFalse($rule->test('FOO BAR $'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new Symbol('$%&@!', 0, 3);

        self::assertTrue($rule->test('FOO bar $$@'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new Symbol('$%&@!', 0, 3);

        self::assertFalse($rule->test('foo bar $$@!'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new Symbol('$%&@!', 0, 3, 1);

        self::assertTrue($rule->test('foo bar $$@!', 2));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new Symbol('$%&@!', 1, null);

        $rule->enforce('&');

        // Force generation of code coverage
        $ruleConstruct = new Symbol('$%&@!', 1, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new Symbol('$%&@!', 1, null);

        $this->expectException(RuleException::class);

        $rule->enforce('€');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraint(): void
    {
        $rule = new Symbol('$%&@!', 5, null);

        $this->expectExceptionMessage('There must be at least 5 symbols ($%&@!).');

        $rule->enforce('foo bar');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraint(): void
    {
        $rule = new Symbol('$%&@!', 0, 10);

        $this->expectExceptionMessage('There must be at most 10 symbols ($%&@!).');

        $rule->enforce('foo bar $$@! $$@! $$@!');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new Symbol('$%&@!', 5, 10);

        $this->expectExceptionMessage('There must be between 5 and 10 symbols ($%&@!).');

        $rule->enforce('foo bar $$@!');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraintEqualToZero(): void
    {
        $rule = new Symbol('$%&@!', 0, 0);

        $this->expectExceptionMessage('There must be no symbols ($%&@!).');

        $rule->enforce('foo bar $$@!');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new Symbol('$%&@!', 3, 3);

        $this->expectExceptionMessage('There must be exactly 3 symbols ($%&@!).');

        $rule->enforce('foo bar $$@!');
    }
}
