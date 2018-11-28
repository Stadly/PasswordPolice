<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Password;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\GuessableData
 * @covers ::<protected>
 * @covers ::<private>
 */
final class GuessableDataTest extends TestCase
{
    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new GuessableData();

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanContainString(): void
    {
        $rule = new GuessableData();
        $password = new Password('foobar', ['oba']);

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanNotContainString(): void
    {
        $rule = new GuessableData();
        $password = new Password('foo28/11/18bar', ['oba']);

        self::assertTrue($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanContainDate(): void
    {
        $rule = new GuessableData();
        $password = new Password('foo28/11/18bar', [new DateTime('2018-11-28')]);

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanNotContainDate(): void
    {
        $rule = new GuessableData();
        $password = new Password('foobar', [new DateTime('2018-11-28')]);

        self::assertTrue($rule->test($password));
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new GuessableData();
        $password = new Password('test', ['oba', new DateTime('2018-11-28')]);

        $rule->enforce($password);

        // Force generation of code coverage
        $ruleConstruct = new GuessableData();
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new GuessableData();
        $password = new Password('foobar', ['oba', new DateTime('2018-11-28')]);

        $this->expectException(RuleException::class);

        $rule->enforce($password);
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageForRuleWithMinConstraint(): void
    {
        $rule = new GuessableData();

        self::assertSame('Must not contain guessable data.', $rule->getMessage());
    }
}
