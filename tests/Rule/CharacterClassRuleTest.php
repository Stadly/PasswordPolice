<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\ValidationError;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\CharacterClassRule
 * @covers ::<protected>
 * @covers ::<private>
 */
final class CharacterClassRuleTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 5, null]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 5, null]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 0, 10]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 0, 10]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 5, 10]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 5, 10]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', -10, null]);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 10, 5]);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 0, null]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 0, null]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 5, 5]);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 5, 5]);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNoCharacters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['']);
    }

    /**
     * @covers ::getCharacters
     */
    public function testCanGetCharacters(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!']);

        self::assertSame('$%&@!', $rule->getCharacters());
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 5, 5, 1]);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 5, 5, 1]);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 5, 5, 1]);
        $rule->addConstraint(10, 10, 2);

        $ruleConstruct = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 10, 10, 2]);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 2, null]);

        self::assertTrue($rule->test('FOO bar $$@'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 2, null]);

        self::assertFalse($rule->test('FOO BAR $'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 0, 3]);

        self::assertTrue($rule->test('FOO bar $$@'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 0, 3]);

        self::assertFalse($rule->test('foo bar $$@!'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 0, 3, 1]);

        self::assertTrue($rule->test('foo bar $$@!', 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 1, null]);

        self::assertNull($rule->validate('&'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeInvalidated(): void
    {
        $rule = $this->getMockForAbstractClass(CharacterClassRule::class, ['$%&@!', 1, null]);
        $rule->method('getMessage')->willReturn('foo');

        self::assertEquals(
            new ValidationError('foo', '€', $rule, 1),
            $rule->validate('€')
        );
    }
}