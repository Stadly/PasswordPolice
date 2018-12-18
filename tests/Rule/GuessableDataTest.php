<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\WordConverter\WordConverterInterface;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\GuessableData
 * @covers ::<protected>
 * @covers ::<private>
 */
final class GuessableDataTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRule(): void
    {
        $rule = new GuessableData();

        // Force generation of code coverage
        $ruleConstruct = new GuessableData();
        self::assertEquals($rule, $ruleConstruct);
    }

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
     * @covers ::test
     */
    public function testStringIsRecognizedAfterSingleWordConverter(): void
    {
        $wordConverter = $this->createMock(WordConverterInterface::class);
        $wordConverter->method('convert')->willReturnCallback(
            function ($word) {
                yield str_replace(['4', '€'], ['a', 'e'], $word);
            }
        );

        $rule = new GuessableData([$wordConverter]);

        self::assertFalse($rule->test(new Password('pine4ppl€jack', ['apple'])));
        self::assertTrue($rule->test(new Password('pine4pp1€jack', ['apple'])));
    }

    /**
     * @covers ::test
     */
    public function testDateIsRecognizedAfterSingleWordConverter(): void
    {
        $wordConverter = $this->createMock(WordConverterInterface::class);
        $wordConverter->method('convert')->willReturnCallback(
            function ($word) {
                yield str_replace(['I', 'B'], ['1', '8'], $word);
            }
        );

        $rule = new GuessableData([$wordConverter]);

        self::assertFalse($rule->test(new Password('foo2B/II/1Bbar', [new DateTime('2018-11-28')])));
        self::assertTrue($rule->test(new Password('fooZB/I!/1Bbar', [new DateTime('2018-11-28')])));
    }

    /**
     * @covers ::test
     */
    public function testStringIsRecognizedAfterMultipleWordConverters(): void
    {
        $wordConverter1 = $this->createMock(WordConverterInterface::class);
        $wordConverter1->method('convert')->willReturnCallback(
            function ($word) {
                yield str_replace(['4'], ['a'], $word);
            }
        );

        $wordConverter2 = $this->createMock(WordConverterInterface::class);
        $wordConverter2->method('convert')->willReturnCallback(
            function ($word) {
                yield str_replace(['€'], ['e'], $word);
            }
        );

        $rule = new GuessableData([$wordConverter1, $wordConverter2]);

        self::assertTrue($rule->test(new Password('pine4ppl€jack', ['apple'])));
        self::assertFalse($rule->test(new Password('pineappl€jack', ['apple'])));
        self::assertFalse($rule->test(new Password('pine4pplejack', ['apple'])));
    }

    /**
     * @covers ::test
     */
    public function testDateIsRecognizedAfterMultipleWordConverters(): void
    {
        $wordConverter1 = $this->createMock(WordConverterInterface::class);
        $wordConverter1->method('convert')->willReturnCallback(
            function ($word) {
                yield str_replace(['I'], ['1'], $word);
            }
        );

        $wordConverter2 = $this->createMock(WordConverterInterface::class);
        $wordConverter2->method('convert')->willReturnCallback(
            function ($word) {
                yield str_replace(['B'], ['8'], $word);
            }
        );

        $rule = new GuessableData([$wordConverter1, $wordConverter2]);

        self::assertTrue($rule->test(new Password('foo2B/I!/1Bbar', [new DateTime('2018-11-28')])));
        self::assertFalse($rule->test(new Password('foo28/II/18bar', [new DateTime('2018-11-28')])));
        self::assertFalse($rule->test(new Password('foo2B/11/1Bbar', [new DateTime('2018-11-28')])));
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
     * @covers ::enforce
     */
    public function testValidationMessageForRuleWithMinConstraint(): void
    {
        $rule = new GuessableData();
        $password = new Password('foobar', ['oba', new DateTime('2018-11-28')]);

        $this->expectExceptionMessage('Must not contain guessable data.');

        $rule->enforce($password);
    }
}
