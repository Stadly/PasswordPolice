<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordConverter;

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
    public function testPasswordCanContainGuessableStringInRule(): void
    {
        $rule = new GuessableData(['oba']);
        $password = new Password('foobar');

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanContainGuessableStringInPassword(): void
    {
        $rule = new GuessableData();
        $password = new Password('foobar', ['oba']);

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanNotContainGuessableString(): void
    {
        $rule = new GuessableData(['oob']);
        $password = new Password('foo28/11/18bar', ['oba']);

        self::assertTrue($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanContainGuessableDateInRule(): void
    {
        $rule = new GuessableData([new DateTime('2018-11-28')]);
        $password = new Password('foo28/11/18bar');

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanContainGuessableDateInPassword(): void
    {
        $rule = new GuessableData();
        $password = new Password('foo28/11/18bar', [new DateTime('2018-11-28')]);

        self::assertFalse($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testPasswordCanNotContainGuessableDate(): void
    {
        $rule = new GuessableData();
        $password = new Password('foobar', [new DateTime('2018-11-28')]);

        self::assertTrue($rule->test($password));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new GuessableData([], [], 1);
        $password = new Password('foobar', ['oba']);

        self::assertTrue($rule->test($password, 2));
    }

    /**
     * @covers ::test
     */
    public function testStringIsRecognizedAfterSingleWordConverter(): void
    {
        $wordConverter = $this->createMock(WordConverter::class);
        $wordConverter->method('convert')->willReturnCallback(
            static function ($word) {
                yield str_replace(['4', '€'], ['a', 'e'], $word);
            }
        );

        $rule = new GuessableData([], [$wordConverter]);

        self::assertFalse($rule->test(new Password('pine4ppl€jack', ['apple'])));
        self::assertTrue($rule->test(new Password('pine4pp1€jack', ['apple'])));
    }

    /**
     * @covers ::test
     */
    public function testDateIsRecognizedAfterSingleWordConverter(): void
    {
        $wordConverter = $this->createMock(WordConverter::class);
        $wordConverter->method('convert')->willReturnCallback(
            static function ($word) {
                yield str_replace(['I', 'B'], ['1', '8'], $word);
            }
        );

        $rule = new GuessableData([], [$wordConverter]);

        self::assertFalse($rule->test(new Password('foo2B/II/1Bbar', [new DateTime('2018-11-28')])));
        self::assertTrue($rule->test(new Password('fooZB/I!/1Bbar', [new DateTime('2018-11-28')])));
    }

    /**
     * @covers ::test
     */
    public function testStringIsRecognizedAfterMultipleWordConverters(): void
    {
        $wordConverter1 = $this->createMock(WordConverter::class);
        $wordConverter1->method('convert')->willReturnCallback(
            static function ($word) {
                yield str_replace(['4'], ['a'], $word);
            }
        );

        $wordConverter2 = $this->createMock(WordConverter::class);
        $wordConverter2->method('convert')->willReturnCallback(
            static function ($word) {
                yield str_replace(['€'], ['e'], $word);
            }
        );

        $rule = new GuessableData([], [$wordConverter1, $wordConverter2]);

        self::assertTrue($rule->test(new Password('pine4ppl€jack', ['apple'])));
        self::assertFalse($rule->test(new Password('pineappl€jack', ['apple'])));
        self::assertFalse($rule->test(new Password('pine4pplejack', ['apple'])));
    }

    /**
     * @covers ::test
     */
    public function testDateIsRecognizedAfterMultipleWordConverters(): void
    {
        $wordConverter1 = $this->createMock(WordConverter::class);
        $wordConverter1->method('convert')->willReturnCallback(
            static function ($word) {
                yield str_replace(['I'], ['1'], $word);
            }
        );

        $wordConverter2 = $this->createMock(WordConverter::class);
        $wordConverter2->method('convert')->willReturnCallback(
            static function ($word) {
                yield str_replace(['B'], ['8'], $word);
            }
        );

        $rule = new GuessableData([], [$wordConverter1, $wordConverter2]);

        self::assertTrue($rule->test(new Password('foo2B/I!/1Bbar', [new DateTime('2018-11-28')])));
        self::assertFalse($rule->test(new Password('foo28/II/18bar', [new DateTime('2018-11-28')])));
        self::assertFalse($rule->test(new Password('foo2B/11/1Bbar', [new DateTime('2018-11-28')])));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new GuessableData();
        $password = new Password('test', ['oba', new DateTime('2018-11-28')]);

        self::assertNull($rule->validate($password));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeInvalidated(): void
    {
        $rule = new GuessableData();
        $password = new Password('foobar', ['oba', new DateTime('2018-11-28')]);

        self::assertEquals(
            new ValidationError('Must not contain guessable data.', $password, $rule, 1),
            $rule->validate($password)
        );
    }
}
