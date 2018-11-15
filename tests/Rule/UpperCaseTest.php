<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

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
        $rule = new UpperCase(5);

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(5);
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

        $rule = new UpperCase(-10);
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
    public function testCannotConstructUnconstrainedRule(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new UpperCase(0);
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
     * @covers ::getMin
     */
    public function testCanGetMinConstraint(): void
    {
        $rule = new UpperCase(5, 10);

        self::assertSame(5, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMaxConstraint(): void
    {
        $rule = new UpperCase(5, 10);

        self::assertSame(10, $rule->getMax());
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new UpperCase(2);

        self::assertTrue($rule->test('foo BAR'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new UpperCase(2);

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
        $rule = new UpperCase(1);

        self::assertTrue($rule->test('Á'));
    }

    /**
     * @covers ::test
     */
    public function testLowerCaseUtf8IsNotCounted(): void
    {
        $rule = new UpperCase(1);

        self::assertFalse($rule->test('á'));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new UpperCase(1);
        $translator = new Translator('en_US');

        $rule->enforce('FOO', $translator);

        // Force generation of code coverage
        $ruleConstruct = new UpperCase(1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new UpperCase(1);
        $translator = new Translator('en_US');

        $this->expectException(RuleException::class);

        $rule->enforce('foo', $translator);
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMinConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new UpperCase(5);

        self::assertSame('There must be at least 5 upper case characters.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new UpperCase(0, 10);

        self::assertSame('There must be at most 10 upper case characters.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new UpperCase(5, 10);

        self::assertSame('There must be between 5 and 10 upper case characters.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMaxConstraintEqualToZero(): void
    {
        $translator = new Translator('en_US');
        $rule = new UpperCase(0, 0);

        self::assertSame('There must be no upper case characters.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new UpperCase(3, 3);

        self::assertSame('There must be exactly 3 upper case characters.', $rule->getMessage($translator));
    }
}
