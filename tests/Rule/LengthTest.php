<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\Length
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LengthTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new Length(5, null);

        // Force generation of code coverage
        $ruleConstruct = new Length(5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new Length(0, 10);

        // Force generation of code coverage
        $ruleConstruct = new Length(0, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new Length(5, 10);

        // Force generation of code coverage
        $ruleConstruct = new Length(5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Length(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Length(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new Length(0, null);

        // Force generation of code coverage
        $ruleConstruct = new Length(0, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new Length(5, 5);

        // Force generation of code coverage
        $ruleConstruct = new Length(5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new Length(5, 5, 1);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = new Length(5, 5, 1);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new Length(5, 5, 1);
        $rule->addConstraint(10, 10, 2);

        // Force generation of code coverage
        $ruleConstruct = new Length(10, 10, 2);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new Length(2, null);

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new Length(2, null);

        self::assertFalse($rule->test('f'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new Length(0, 3);

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new Length(0, 3);

        self::assertFalse($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testUtf8IsTreatedAsSingleCharacter(): void
    {
        $rule = new Length(2, 2);

        self::assertTrue($rule->test('áÁ'));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new Length(1, null);

        $rule->enforce('foo');

        // Force generation of code coverage
        $ruleConstruct = new Length(1, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new Length(1, null);

        $this->expectException(RuleException::class);

        $rule->enforce('');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraint(): void
    {
        $rule = new Length(5, null);

        $this->expectExceptionMessage('There must be at least 5 characters.');

        $rule->enforce('fo');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraint(): void
    {
        $rule = new Length(0, 10);

        $this->expectExceptionMessage('There must be at most 10 characters.');

        $rule->enforce('fo bar qwerty');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new Length(5, 10);

        $this->expectExceptionMessage('There must be between 5 and 10 characters.');

        $rule->enforce('fo');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraintEqualToZero(): void
    {
        $rule = new Length(0, 0);

        $this->expectExceptionMessage('There must be no characters.');

        $rule->enforce('fo');
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new Length(3, 3);

        $this->expectExceptionMessage('There must be exactly 3 characters.');

        $rule->enforce('fo');
    }
}
