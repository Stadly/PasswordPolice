<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

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
        $rule = new LowerCase(5);

        // Force generation of code coverage
        $ruleConstruct = new LowerCase(5);
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

        $rule = new LowerCase(-10);
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

        $rule = new LowerCase(0);
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
    public function testCanGetMin(): void
    {
        $rule = new LowerCase(5, 10);

        self::assertSame(5, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMax(): void
    {
        $rule = new LowerCase(5, 10);

        self::assertSame(10, $rule->getMax());
    }

    /**
     * @covers ::test
     */
    public function testCanTestMinConstraintIsTrue(): void
    {
        $rule = new LowerCase(2);

        self::assertTrue($rule->test('FOO bar'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestMinConstraintIsFalse(): void
    {
        $rule = new LowerCase(2);

        self::assertFalse($rule->test('FOO BAR'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestMaxConstraintIsTrue(): void
    {
        $rule = new LowerCase(0, 3);

        self::assertTrue($rule->test('FOO bar'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestMaxConstraintIsFalse(): void
    {
        $rule = new LowerCase(0, 3);

        self::assertFalse($rule->test('foo bar'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestCountsLowerCaseUtf8(): void
    {
        $rule = new LowerCase(1);

        self::assertTrue($rule->test('á'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestDoesNotCountUpperCaseUtf8(): void
    {
        $rule = new LowerCase(1);

        self::assertFalse($rule->test('Á'));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new LowerCase(1);
        $translator = new Translator('en_US');

        $rule->enforce('foo', $translator);

        // Force generation of code coverage
        $ruleConstruct = new LowerCase(1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new LowerCase(1);
        $translator = new Translator('en_US');

        $this->expectException(LowerCaseException::class);

        $rule->enforce('FOO', $translator);
    }
}
