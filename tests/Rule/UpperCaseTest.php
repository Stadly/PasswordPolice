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
    public function testCanGetMin(): void
    {
        $rule = new UpperCase(5, 10);

        self::assertSame(5, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMax(): void
    {
        $rule = new UpperCase(5, 10);

        self::assertSame(10, $rule->getMax());
    }

    /**
     * @covers ::test
     */
    public function testCanTestMinConstraintIsTrue(): void
    {
        $rule = new UpperCase(2);

        self::assertTrue($rule->test('foo BAR'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestMinConstraintIsFalse(): void
    {
        $rule = new UpperCase(2);

        self::assertFalse($rule->test('foo bar'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestMaxConstraintIsTrue(): void
    {
        $rule = new UpperCase(0, 3);

        self::assertTrue($rule->test('foo BAR'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestMaxConstraintIsFalse(): void
    {
        $rule = new UpperCase(0, 3);

        self::assertFalse($rule->test('FOO BAR'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestCountsUpperCaseUtf8(): void
    {
        $rule = new UpperCase(1);

        self::assertTrue($rule->test('Á'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestDoesNotCountLowerCaseUtf8(): void
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
        $translator = new Translator('en_EN');

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
        $translator = new Translator('en_EN');

        $this->expectException(UpperCaseException::class);

        $rule->enforce('foo', $translator);
    }
}
