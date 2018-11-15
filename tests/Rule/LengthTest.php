<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;
use Stadly\PasswordPolice\RuleException;

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
    public function testCanGetMinConstraint(): void
    {
        $rule = new Length(5, 10);

        self::assertSame(5, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMaxConstraint(): void
    {
        $rule = new Length(5, 10);

        self::assertSame(10, $rule->getMax());
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new Length(2);

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new Length(2);

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

        $this->expectException(RuleException::class);

        $rule->enforce('', $translator);
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMinConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new Length(5);

        self::assertSame('There must be at least 5 characters.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new Length(0, 10);

        self::assertSame('There must be at most 10 characters.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new Length(5, 10);

        self::assertSame('There must be between 5 and 10 characters.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMaxConstraintEqualToZero(): void
    {
        $translator = new Translator('en_US');
        $rule = new Length(0, 0);

        self::assertSame('There must be no characters.', $rule->getMessage($translator));
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $rule = new Length(3, 3);

        self::assertSame('There must be exactly 3 characters.', $rule->getMessage($translator));
    }
}
