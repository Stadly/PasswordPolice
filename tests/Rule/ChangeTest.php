<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use DateInterval;
use InvalidArgumentException;
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\Change
 * @covers ::<protected>
 * @covers ::<private>
 */
final class ChangeTest extends TestCase
{
    /**
     * @var Password
     */
    private $password;

    protected function setUp(): void
    {
        $this->password = new Password('foobar', [], [
            new FormerPassword('qwerty', new DateTime('- 7 days')),
            new FormerPassword('baz', new DateTime('-1 month')),
            new FormerPassword('bar', new DateTime('-1 year')),
        ]);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new Change(new DateInterval('P5D'), null);

        // Force generation of code coverage
        $ruleConstruct = new Change(new DateInterval('P5D'), null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new Change(new DateInterval('PT0S'), new DateInterval('P10D'));

        // Force generation of code coverage
        $ruleConstruct = new Change(new DateInterval('PT0S'), new DateInterval('P10D'));
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new Change(new DateInterval('P5D'), new DateInterval('P10D'));

        // Force generation of code coverage
        $ruleConstruct = new Change(new DateInterval('P5D'), new DateInterval('P10D'));
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Change(DateInterval::createFromDateString('- 5 days'), null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Change(new DateInterval('P10D'), new DateInterval('P5D'));
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new Change(new DateInterval('PT0S'), null);

        // Force generation of code coverage
        $ruleConstruct = new Change(new DateInterval('PT0S'), null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new Change(new DateInterval('P5D'), new DateInterval('P5D'));

        // Force generation of code coverage
        $ruleConstruct = new Change(new DateInterval('P5D'), new DateInterval('P5D'));
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new Change(new DateInterval('P5D'), new DateInterval('P5D'), 1);
        $rule->addConstraint(new DateInterval('P10D'), new DateInterval('P10D'), 1);

        // Force generation of code coverage
        $ruleConstruct = new Change(new DateInterval('P5D'), new DateInterval('P5D'), 1);
        $ruleConstruct->addConstraint(new DateInterval('P10D'), new DateInterval('P10D'), 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new Change(new DateInterval('P5D'), new DateInterval('P5D'), 1);
        $rule->addConstraint(new DateInterval('P10D'), new DateInterval('P10D'), 2);

        // Force generation of code coverage
        $ruleConstruct = new Change(new DateInterval('P10D'), new DateInterval('P10D'), 2);
        $ruleConstruct->addConstraint(new DateInterval('P5D'), new DateInterval('P5D'), 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new Change(new DateInterval('P10D'), null);

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new Change(new DateInterval('P5D'), null);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new Change(new DateInterval('P10D'), null);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new Change(new DateInterval('PT0S'), new DateInterval('P10D'));

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new Change(new DateInterval('PT0S'), new DateInterval('P5D'));

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new Change(new DateInterval('PT0S'), new DateInterval('P5D'), 1);

        self::assertTrue($rule->test($this->password, 2));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new Change(new DateInterval('P5D'), null);

        $rule->enforce($this->password);

        // Force generation of code coverage
        $ruleConstruct = new Change(new DateInterval('P5D'), null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new Change(new DateInterval('P10D'), null);

        $this->expectException(RuleException::class);

        $rule->enforce($this->password);
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraint(): void
    {
        $rule = new Change(new DateInterval('P10D'), null);

        $this->expectExceptionMessage('Must be at least 1 week 3 days between password changes.');

        $rule->enforce($this->password);
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMaxConstraint(): void
    {
        $rule = new Change(new DateInterval('PT0S'), new DateInterval('P5D'));

        $this->expectExceptionMessage('Must be at most 5 days between password changes.');

        $rule->enforce($this->password);
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new Change(new DateInterval('P14D'), new DateInterval('P1M'));

        $this->expectExceptionMessage('Must be between 2 weeks and 1 month between password changes.');

        $rule->enforce($this->password);
    }

    /**
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new Change(new DateInterval('P6D'), new DateInterval('PT144H'));

        $this->expectExceptionMessage('Must be exactly 6 days between password changes.');

        $rule->enforce($this->password);
    }
}
