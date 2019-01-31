<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\DateFormatter;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\GuessableDataRule
 * @covers ::<protected>
 * @covers ::<private>
 */
final class GuessableDataRuleTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRule(): void
    {
        $rule = new GuessableDataRule();

        // Force generation of code coverage
        $ruleConstruct = new GuessableDataRule();
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new GuessableDataRule();

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanContainGuessableStringInRule(): void
    {
        $rule = new GuessableDataRule(['oba']);
        $password = new Password('foobar');

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanContainGuessableStringInPassword(): void
    {
        $rule = new GuessableDataRule();
        $password = new Password('foobar', ['oba']);

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanNotContainGuessableString(): void
    {
        $rule = new GuessableDataRule(['oob']);
        $password = new Password('foo28/11/18bar', ['oba']);

        self::assertTrue($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanContainGuessableDateInRule(): void
    {
        $rule = new GuessableDataRule([new DateTime('2018-11-28')]);
        $password = new Password('foo28/11/18bar');

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordDoesNotContainEmptyString(): void
    {
        $rule = new GuessableDataRule(['']);
        $password = new Password('foobar');

        self::assertTrue($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanContainGuessableDateInPassword(): void
    {
        $rule = new GuessableDataRule();
        $password = new Password('foo28/11/18bar', [new DateTime('2018-11-28')]);

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanNotContainGuessableDate(): void
    {
        $rule = new GuessableDataRule();
        $password = new Password('foobar', [new DateTime('2018-11-28')]);

        self::assertTrue($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new GuessableDataRule([], [], null, 1);
        $password = new Password('foobar', ['oba']);

        self::assertTrue($rule->test($password, 2));
    }

    /**
     * @covers ::test
     */
    public function testStringIsRecognizedAfterSingleWordFormatter(): void
    {
        $wordFormatter = $this->createMock(WordFormatter::class);
        $wordFormatter->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace(['4', '€'], ['a', 'e'], $word);
                }
            }
        );

        $rule = new GuessableDataRule([], [$wordFormatter]);

        self::assertFalse($rule->test(new Password('pine4ppl€jack', ['apple'])));
        self::assertTrue($rule->test(new Password('pine4pp1€jack', ['apple'])));
    }

    /**
     * @covers ::test
     */
    public function testDateIsRecognizedAfterSingleWordFormatter(): void
    {
        $wordFormatter = $this->createMock(WordFormatter::class);
        $wordFormatter->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace(['I', 'B'], ['1', '8'], $word);
                }
            }
        );

        $rule = new GuessableDataRule([], [$wordFormatter]);

        self::assertFalse($rule->test(new Password('foo2B/II/1Bbar', [new DateTime('2018-11-28')])));
        self::assertTrue($rule->test(new Password('fooZB/I!/1Bbar', [new DateTime('2018-11-28')])));
    }

    /**
     * @covers ::test
     */
    public function testStringIsRecognizedAfterMultipleWordFormatters(): void
    {
        $wordFormatter1 = $this->createMock(WordFormatter::class);
        $wordFormatter1->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace(['4'], ['a'], $word);
                }
            }
        );

        $wordFormatter2 = $this->createMock(WordFormatter::class);
        $wordFormatter2->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace(['€'], ['e'], $word);
                }
            }
        );

        $rule = new GuessableDataRule([], [$wordFormatter1, $wordFormatter2]);

        self::assertTrue($rule->test(new Password('pine4ppl€jack', ['apple'])));
        self::assertFalse($rule->test(new Password('pineappl€jack', ['apple'])));
        self::assertFalse($rule->test(new Password('pine4pplejack', ['apple'])));
    }

    /**
     * @covers ::test
     */
    public function testDateIsRecognizedAfterMultipleWordFormatters(): void
    {
        $wordFormatter1 = $this->createMock(WordFormatter::class);
        $wordFormatter1->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace(['I'], ['1'], $word);
                }
            }
        );

        $wordFormatter2 = $this->createMock(WordFormatter::class);
        $wordFormatter2->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace(['B'], ['8'], $word);
                }
            }
        );

        $rule = new GuessableDataRule([], [$wordFormatter1, $wordFormatter2]);

        self::assertTrue($rule->test(new Password('foo2B/I!/1Bbar', [new DateTime('2018-11-28')])));
        self::assertFalse($rule->test(new Password('foo28/II/18bar', [new DateTime('2018-11-28')])));
        self::assertFalse($rule->test(new Password('foo2B/11/1Bbar', [new DateTime('2018-11-28')])));
    }

    /**
     * @covers ::test
     */
    public function testUnformattedStringIsRecognizedAfterWordFormatter(): void
    {
        $wordFormatter = $this->createMock(WordFormatter::class);
        $wordFormatter->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace('a', 'f', $word);
                }
            }
        );

        $rule = new GuessableDataRule([], [$wordFormatter]);

        self::assertFalse($rule->test(new Password('apple', ['apple'])));
        self::assertFalse($rule->test(new Password('apple', ['fpple'])));
    }

    /**
     * @covers ::test
     */
    public function testDateFormatterCanBeCustomized(): void
    {
        $dateFormatter = $this->createMock(DateFormatter::class);
        $dateFormatter->method('apply')->willReturnCallback(
            static function (iterable $dates): Traversable {
                foreach ($dates as $date) {
                    yield $date->format('m\o\n\t\h');
                }
            }
        );

        $rule = new GuessableDataRule([], [], $dateFormatter);

        self::assertFalse($rule->test(new Password('test 11onth foo', [new DateTime('2018-11-28')])));
        self::assertTrue($rule->test(new Password('2018-11-28', [new DateTime('2018-11-28')])));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new GuessableDataRule();
        $password = new Password('test', ['oba', new DateTime('2018-11-28')]);

        self::assertNull($rule->validate($password));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeInvalidated(): void
    {
        $rule = new GuessableDataRule();
        $password = new Password('foobar', ['oba', new DateTime('2018-11-28')]);

        self::assertEquals(
            new ValidationError(
                'The password cannot words that are easy to guess.',
                $password,
                $rule,
                1
            ),
            $rule->validate($password)
        );
    }
}
