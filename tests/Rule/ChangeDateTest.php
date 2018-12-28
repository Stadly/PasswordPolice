<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\ValidationError;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\ChangeDate
 * @covers ::<protected>
 * @covers ::<private>
 */
final class ChangeDateTest extends TestCase
{
    /**
     * @var Password
     */
    private $password;

    protected function setUp(): void
    {
        $this->password = new Password('foobar', [], [
            new FormerPassword('qwerty', new DateTimeImmutable('2003-04-05')),
            new FormerPassword('baz', new DateTimeImmutable('2002-03-04')),
            new FormerPassword('bar', new DateTimeImmutable('2001-02-03')),
        ]);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new ChangeDate(new DateTime('2001-02-03'), null);

        // Force generation of code coverage
        $ruleConstruct = new ChangeDate(new DateTime('2001-02-03'), null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new ChangeDate(null, new DateTime('2002-03-04'));

        // Force generation of code coverage
        $ruleConstruct = new ChangeDate(null, new DateTime('2002-03-04'));
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new ChangeDate(new DateTime('2001-02-03'), new DateTime('2002-03-04'));

        // Force generation of code coverage
        $ruleConstruct = new ChangeDate(new DateTime('2001-02-03'), new DateTime('2002-03-04'));
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new ChangeDate(new DateTime('2002-03-04'), new DateTime('2001-02-03'));
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new ChangeDate(null, null);

        // Force generation of code coverage
        $ruleConstruct = new ChangeDate(null, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new ChangeDate(new DateTime('2001-02-03'), new DateTime('2001-02-03'));

        // Force generation of code coverage
        $ruleConstruct = new ChangeDate(new DateTime('2001-02-03'), new DateTime('2001-02-03'));
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new ChangeDate(new DateTime('2001-02-03'), new DateTime('2001-02-03'), 1);
        $rule->addConstraint(new DateTime('2002-03-04'), new DateTime('2002-03-04'), 1);

        // Force generation of code coverage
        $ruleConstruct = new ChangeDate(new DateTime('2001-02-03'), new DateTime('2001-02-03'), 1);
        $ruleConstruct->addConstraint(new DateTime('2002-03-04'), new DateTime('2002-03-04'), 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new ChangeDate(new DateTime('2001-02-03'), new DateTime('2001-02-03'), 1);
        $rule->addConstraint(new DateTime('2002-03-04'), new DateTime('2002-03-04'), 2);

        $ruleConstruct = new ChangeDate(new DateTime('2002-03-04'), new DateTime('2002-03-04'), 2);
        $ruleConstruct->addConstraint(new DateTime('2001-02-03'), new DateTime('2001-02-03'), 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new ChangeDate(new DateTime(), null);

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new ChangeDate(new DateTime('2003-04-04'), null);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new ChangeDate(new DateTime('2003-04-06'), null);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new ChangeDate(null, new DateTime('2003-04-06'));

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new ChangeDate(null, new DateTime('2003-04-04'));

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new ChangeDate(null, new DateTime('2003-04-04'), 1);

        self::assertTrue($rule->test($this->password, 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new ChangeDate(new DateTime('2003-04-04'), null);

        self::assertNull($rule->validate($this->password));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new ChangeDate(new DateTime('2003-04-06'), null);

        self::assertEquals(
            new ValidationError(
                'The password must have been changed on or after 2003-04-06 00:00:00.',
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
        $rule = new ChangeDate(null, new DateTime('2003-04-04'));

        self::assertEquals(
            new ValidationError(
                'The password must have been changed on or before 2003-04-04 00:00:00.',
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
        $rule = new ChangeDate(new DateTime('2003-04-06'), new DateTime('2003-04-07'));

        self::assertEquals(
            new ValidationError(
                'The password must have been changed between 2003-04-06 00:00:00 and 2003-04-07 00:00:00.',
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
        $rule = new ChangeDate(new DateTime('2003-04-06'), new DateTime('2003-04-06'));

        self::assertEquals(
            new ValidationError(
                'The password must have been changed at 2003-04-06 00:00:00.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password)
        );
    }
}
