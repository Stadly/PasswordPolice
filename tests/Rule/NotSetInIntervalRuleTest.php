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
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\NotSetInIntervalRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class NotSetInIntervalRuleTest extends TestCase
{
    /**
     * @var Password
     */
    private $password;

    protected function setUp(): void
    {
        $this->password = new Password('foobar', [], [
            new FormerPassword(new DateTimeImmutable('2003-04-05')),
            new FormerPassword(new DateTimeImmutable('2002-03-04'), 'baz'),
            new FormerPassword(new DateTimeImmutable('2001-02-03'), 'bar'),
        ]);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithEndConstraint(): void
    {
        new NotSetInIntervalRule(new DateTime('2002-03-04'), null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithStartConstraint(): void
    {
        new NotSetInIntervalRule(null, new DateTime('2001-02-03'));
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithBothEndAndStartConstraint(): void
    {
        new NotSetInIntervalRule(new DateTime('2002-03-04'), new DateTime('2001-02-03'));
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithStartConstraintLargerThanEndConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new NotSetInIntervalRule(new DateTime('2001-02-03'), new DateTime('2002-03-04'));
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructUnconstrainedRule(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new NotSetInIntervalRule(null, null);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithEndConstraintEqualToStartConstraint(): void
    {
        new NotSetInIntervalRule(new DateTime('2002-03-04'), new DateTime('2002-03-04'));
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule1 = new NotSetInIntervalRule(new DateTime('2002-03-04'), new DateTime('2002-03-04'), 1);
        $rule1->addConstraint(new DateTime('2001-02-03'), new DateTime('2001-02-03'), 2);

        $rule2 = new NotSetInIntervalRule(new DateTime('2001-02-03'), new DateTime('2001-02-03'), 2);
        $rule2->addConstraint(new DateTime('2002-03-04'), new DateTime('2002-03-04'), 1);
        self::assertEquals($rule1, $rule2);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCannotAddUnconstrainedConstraint(): void
    {
        $rule = new NotSetInIntervalRule(new DateTime('2002-03-04'), null);

        $this->expectException(InvalidArgumentException::class);

        $rule->addConstraint(null, null);
    }

    /**
     * @covers ::test
     */
    public function testRuleWithEndContraintInThePastIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new NotSetInIntervalRule(new DateTime('-24 hours'), null);

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testRuleWithEndContraintInTheFutureIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new NotSetInIntervalRule(new DateTime('+24 hours'), null);

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testEndConstraintCanBeSatisfied(): void
    {
        $rule = new NotSetInIntervalRule(new DateTime('2003-04-04'), null);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testEndConstraintCanBeUnsatisfied(): void
    {
        $rule = new NotSetInIntervalRule(new DateTime('2003-04-06'), null);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testStartConstraintCanBeSatisfied(): void
    {
        $rule = new NotSetInIntervalRule(null, new DateTime('2003-04-06'));

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testStartConstraintCanBeUnsatisfied(): void
    {
        $rule = new NotSetInIntervalRule(null, new DateTime('2003-04-04'));

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new NotSetInIntervalRule(null, new DateTime('2003-04-04'), 1);

        self::assertTrue($rule->test($this->password, 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new NotSetInIntervalRule(new DateTime('2003-04-04'), null);

        self::assertNull($rule->validate($this->password, new Translator('en_US')));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithEndConstraintCanBeInvalidated(): void
    {
        $rule = new NotSetInIntervalRule(new DateTime('2003-04-06'), null);

        self::assertEquals(
            new ValidationError(
                'The password must have been set after 2003-04-06 00:00:00.',
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
    public function testRuleWithStartConstraintCanBeInvalidated(): void
    {
        $rule = new NotSetInIntervalRule(null, new DateTime('2003-04-04'));

        self::assertEquals(
            new ValidationError(
                'The password must have been set before 2003-04-04 00:00:00.',
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
    public function testRuleWithBothEndAndStartConstraintCanBeInvalidated(): void
    {
        $rule = new NotSetInIntervalRule(new DateTime('2003-04-06'), new DateTime('2003-04-04'));

        self::assertEquals(
            new ValidationError(
                'The password must have been set before 2003-04-04 00:00:00 or after 2003-04-06 00:00:00.',
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
    public function testRuleWithEndConstraintEqualToStartConstraintCanBeInvalidated(): void
    {
        $rule = new NotSetInIntervalRule(new DateTime('2003-04-05'), new DateTime('2003-04-05'));

        self::assertEquals(
            new ValidationError(
                'The password must have been set before or after 2003-04-05 00:00:00.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password, new Translator('en_US'))
        );
    }
}
