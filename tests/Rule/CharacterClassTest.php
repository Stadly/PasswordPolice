<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\CharacterClass
 * @covers ::<protected>
 * @covers ::<private>
 */
final class CharacterClassTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 5, null]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 5, null]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 0, 10]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 0, 10]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 5, 10]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 5, 10]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', -10, null]);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 10, 5]);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 0, null]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 0, null]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 5, 5]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 5, 5]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNoCharacters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['']);
    }

    /**
     * @covers ::getCharacters
     */
    public function testCanGetCharacters(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!']);

        self::assertSame('$%&@!', $rule->getCharacters());
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 5, 5, 1]);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 5, 5, 1]);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 5, 5, 1]);
        $rule->addConstraint(10, 10, 2);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 10, 10, 2]);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 2, null]);

        self::assertTrue($rule->test('FOO bar $$@'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 2, null]);

        self::assertFalse($rule->test('FOO BAR $'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 0, 3]);

        self::assertTrue($rule->test('FOO bar $$@'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 0, 3]);

        self::assertFalse($rule->test('foo bar $$@!'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 0, 3, 1]);

        self::assertTrue($rule->test('foo bar $$@!', 2));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 1, null]);

        $rule->enforce('&');

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 1, null]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClass::class, ['$%&@!', 1, null]);

        $this->expectException(RuleException::class);

        $rule->enforce('€');
    }
}
