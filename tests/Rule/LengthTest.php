<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

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
        $rule = new Length(5);

        // Force generation of code coverage
        $ruleConstruct = new Length(5);
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

        $rule = new Length(-10);
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
    public function testCannotConstructUnconstrainedRule(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Length(0);
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
     * @covers ::getMin
     */
    public function testCanGetMin(): void
    {
        $rule = new Length(5, 10);

        self::assertSame(5, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMax(): void
    {
        $rule = new Length(5, 10);

        self::assertSame(10, $rule->getMax());
    }

    /**
     * @covers ::test
     */
    public function testCanTestMinConstraintIsTrue(): void
    {
        $rule = new Length(2);

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestMinConstraintIsFalse(): void
    {
        $rule = new Length(2);

        self::assertFalse($rule->test('f'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestMaxConstraintIsTrue(): void
    {
        $rule = new Length(0, 3);

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestMaxConstraintIsFalse(): void
    {
        $rule = new Length(0, 3);

        self::assertFalse($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testCanTestCountsUtf8AsOneCharacter(): void
    {
        $rule = new Length(2, 2);

        self::assertTrue($rule->test('áÁ'));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new Length(1);
        $translator = new Translator('en_US');

        $rule->enforce('foo', $translator);

        // Force generation of code coverage
        $ruleConstruct = new Length(1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new Length(1);
        $translator = new Translator('en_US');

        $this->expectException(LengthException::class);

        $rule->enforce('', $translator);
    }
}
