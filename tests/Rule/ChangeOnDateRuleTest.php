<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\ValidationError;
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\ChangeOnDateRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class ChangeOnDateRuleTest extends TestCase
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
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        new ChangeOnDateRule(new DateTime('2001-02-03'), null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        new ChangeOnDateRule(null, new DateTime('2002-03-04'));
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        new ChangeOnDateRule(new DateTime('2001-02-03'), new DateTime('2002-03-04'));
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ChangeOnDateRule(new DateTime('2002-03-04'), new DateTime('2001-02-03'));
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        new ChangeOnDateRule(null, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        new ChangeOnDateRule(new DateTime('2001-02-03'), new DateTime('2001-02-03'));
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule1 = new ChangeOnDateRule(new DateTime('2001-02-03'), new DateTime('2001-02-03'), 1);
        $rule1->addConstraint(new DateTime('2002-03-04'), new DateTime('2002-03-04'), 2);

        $rule2 = new ChangeOnDateRule(new DateTime('2002-03-04'), new DateTime('2002-03-04'), 2);
        $rule2->addConstraint(new DateTime('2001-02-03'), new DateTime('2001-02-03'), 1);
        self::assertEquals($rule1, $rule2);
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new ChangeOnDateRule(new DateTime(), null);

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new ChangeOnDateRule(new DateTime('2003-04-04'), null);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new ChangeOnDateRule(new DateTime('2003-04-06'), null);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new ChangeOnDateRule(null, new DateTime('2003-04-06'));

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new ChangeOnDateRule(null, new DateTime('2003-04-04'));

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new ChangeOnDateRule(null, new DateTime('2003-04-04'), 1);

        self::assertTrue($rule->test($this->password, 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new ChangeOnDateRule(new DateTime('2003-04-04'), null);

        self::assertNull($rule->validate($this->password, new Translator('en_US')));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new ChangeOnDateRule(new DateTime('2003-04-06'), null);

        self::assertEquals(
            new ValidationError(
                'The password must last have been changed on or after 2003-04-06 00:00:00.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password, new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintCanBeInvalidated(): void
    {
        $rule = new ChangeOnDateRule(null, new DateTime('2003-04-04'));

        self::assertEquals(
            new ValidationError(
                'The password must last have been changed on or before 2003-04-04 00:00:00.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password, new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithBothMinAndMaxConstraintCanBeInvalidated(): void
    {
        $rule = new ChangeOnDateRule(new DateTime('2003-04-06'), new DateTime('2003-04-07'));

        self::assertEquals(
            new ValidationError(
                'The password must last have been changed between 2003-04-06 00:00:00 and 2003-04-07 00:00:00.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password, new Translator('en_US'))
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintEqualToMaxConstraintCanBeInvalidated(): void
    {
        $rule = new ChangeOnDateRule(new DateTime('2003-04-06'), new DateTime('2003-04-06'));

        self::assertEquals(
            new ValidationError(
                'The password must last have been changed at 2003-04-06 00:00:00.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password, new Translator('en_US'))
        );
    }
}
