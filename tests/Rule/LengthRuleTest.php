<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\ValidationError;
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\LengthRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class LengthRuleTest extends TestCase
{
    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        new LengthRule(5, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        new LengthRule(0, 10);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        new LengthRule(5, 10);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new LengthRule(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new LengthRule(10, 5);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        new LengthRule(0, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        new LengthRule(5, 5);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule1 = new LengthRule(5, 5, 1);
        $rule1->addConstraint(10, 10, 2);

        $rule2 = new LengthRule(10, 10, 2);
        $rule2->addConstraint(5, 5, 1);
        self::assertEquals($rule1, $rule2);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new LengthRule(2, null);

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new LengthRule(2, null);

        self::assertFalse($rule->test('f'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new LengthRule(0, 3);

        self::assertTrue($rule->test('foo'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new LengthRule(0, 3);

        self::assertFalse($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testUtf8IsTreatedAsSingleCharacter(): void
    {
        $rule = new LengthRule(2, 2);

        self::assertTrue($rule->test('áÁ'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new LengthRule(0, 3, 1);

        self::assertTrue($rule->test('foobar', 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new LengthRule(1, null);

        self::assertNull($rule->validate('foo', new Translator('en_US')));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new LengthRule(5, null);

        self::assertEquals(
            new ValidationError(
                'The password must contain at least 5 characters.',
                'fo',
                $rule,
                1
            ),
            $rule->validate('fo', new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintCanBeInvalidated(): void
    {
        $rule = new LengthRule(0, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain at most 10 characters.',
                'fo bar qwerty',
                $rule,
                1
            ),
            $rule->validate('fo bar qwerty', new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithBothMinAndMaxConstraintCanBeInvalidated(): void
    {
        $rule = new LengthRule(5, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain between 5 and 10 characters.',
                'fo',
                $rule,
                1
            ),
            $rule->validate('fo', new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintEqualToZeroCanBeInvalidated(): void
    {
        $rule = new LengthRule(0, 0);

        self::assertEquals(
            new ValidationError(
                'The password cannot contain characters.',
                'fo',
                $rule,
                1
            ),
            $rule->validate('fo', new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintEqualToMaxConstraintCanBeInvalidated(): void
    {
        $rule = new LengthRule(3, 3);

        self::assertEquals(
            new ValidationError(
                'The password must contain exactly 3 characters.',
                'fo',
                $rule,
                1
            ),
            $rule->validate('fo', new Translator('en_US'))
        );
    }
}
