<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateInterval;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\ValidationError;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\ChangeWithIntervalRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ChangeWithIntervalRuleTest extends TestCase
{
    /**
     * @var Password
     */
    private $password;

    protected function setUp(): void
    {
        $this->password = new Password('foobar', [], [
            new FormerPassword('qwerty', new DateTimeImmutable('-6 days')),
            new FormerPassword('baz', new DateTimeImmutable('-1 month')),
            new FormerPassword('bar', new DateTimeImmutable('-1 year')),
        ]);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P5D'), null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('PT0S'), new DateInterval('P10D'));
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P5D'), new DateInterval('P10D'));
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new ChangeWithIntervalRule(DateInterval::createFromDateString('-5 days'), null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new ChangeWithIntervalRule(new DateInterval('P10D'), new DateInterval('P5D'));
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('PT0S'), null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P5D'), new DateInterval('P5D'));
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule1 = new ChangeWithIntervalRule(new DateInterval('P5D'), new DateInterval('P5D'), 1);
        $rule1->addConstraint(new DateInterval('P10D'), new DateInterval('P10D'), 2);

        $rule2 = new ChangeWithIntervalRule(new DateInterval('P10D'), new DateInterval('P10D'), 2);
        $rule2->addConstraint(new DateInterval('P5D'), new DateInterval('P5D'), 1);
        self::assertEquals($rule1, $rule2);
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P10D'), null);

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P5D'), null);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P10D'), null);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('PT0S'), new DateInterval('P10D'));

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('PT0S'), new DateInterval('P5D'));

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('PT0S'), new DateInterval('P5D'), 1);

        self::assertTrue($rule->test($this->password, 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P5D'), null);

        self::assertNull($rule->validate($this->password));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P7D'), null);

        self::assertEquals(
            new ValidationError(
                'There must be at least 1 week between password changes.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password)
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintCanBeInvalidated(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('PT0S'), new DateInterval('P5D'));

        self::assertEquals(
            new ValidationError(
                'There must be at most 5 days between password changes.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password)
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithBothMinAndMaxConstraintCanBeInvalidated(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P14D'), new DateInterval('P1M'));

        self::assertEquals(
            new ValidationError(
                'There must be between 2 weeks and 1 month between password changes.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password)
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintEqualToMaxConstraintCanBeInvalidated(): void
    {
        $rule = new ChangeWithIntervalRule(new DateInterval('P6D'), new DateInterval('PT144H'));

        self::assertEquals(
            new ValidationError(
                'There must be exactly 6 days between password changes.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password)
        );
    }
}
