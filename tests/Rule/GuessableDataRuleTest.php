<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\DateFormatter;
use Stadly\PasswordPolice\Formatter;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\ValidationError;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\GuessableDataRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class GuessableDataRuleTest extends TestCase
{
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
    public function testStringIsRecognizedAfterSingleFormatter(): void
    {
        $formatter = $this->createMock(Formatter::class);
        $formatter->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(str_replace(['4', '€'], ['a', 'e'], $string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $rule = new GuessableDataRule([], [$formatter]);

        self::assertFalse($rule->test(new Password('pine4ppl€jack', ['apple'])));
        self::assertTrue($rule->test(new Password('pine4pp1€jack', ['apple'])));
    }

    /**
     * @covers ::test
     */
    public function testDateIsRecognizedAfterSingleFormatter(): void
    {
        $formatter = $this->createMock(Formatter::class);
        $formatter->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(str_replace(['I', 'B'], ['1', '8'], $string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $rule = new GuessableDataRule([], [$formatter]);

        self::assertFalse($rule->test(new Password('foo2B/II/1Bbar', [new DateTime('2018-11-28')])));
        self::assertTrue($rule->test(new Password('fooZB/I!/1Bbar', [new DateTime('2018-11-28')])));
    }

    /**
     * @covers ::test
     */
    public function testStringIsRecognizedAfterMultipleFormatters(): void
    {
        $formatter1 = $this->createMock(Formatter::class);
        $formatter1->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(str_replace(['4'], ['a'], $string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $formatter2 = $this->createMock(Formatter::class);
        $formatter2->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(str_replace(['€'], ['e'], $string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $rule = new GuessableDataRule([], [$formatter1, $formatter2]);

        self::assertTrue($rule->test(new Password('pine4ppl€jack', ['apple'])));
        self::assertFalse($rule->test(new Password('pineappl€jack', ['apple'])));
        self::assertFalse($rule->test(new Password('pine4pplejack', ['apple'])));
    }

    /**
     * @covers ::test
     */
    public function testDateIsRecognizedAfterMultipleFormatters(): void
    {
        $formatter1 = $this->createMock(Formatter::class);
        $formatter1->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(str_replace(['I'], ['1'], $string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $formatter2 = $this->createMock(Formatter::class);
        $formatter2->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(str_replace(['B'], ['8'], $string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $rule = new GuessableDataRule([], [$formatter1, $formatter2]);

        self::assertTrue($rule->test(new Password('foo2B/I!/1Bbar', [new DateTime('2018-11-28')])));
        self::assertFalse($rule->test(new Password('foo28/II/18bar', [new DateTime('2018-11-28')])));
        self::assertFalse($rule->test(new Password('foo2B/11/1Bbar', [new DateTime('2018-11-28')])));
    }

    /**
     * @covers ::test
     */
    public function testUnformattedStringIsRecognizedAfterFormatter(): void
    {
        $formatter = $this->createMock(Formatter::class);
        $formatter->method('apply')->willReturnCallback(
            static function (CharTree $charTree): CharTree {
                $charTrees = [];
                foreach ($charTree as $string) {
                    $charTrees[] = CharTree::fromString(str_replace('a', 'f', $string));
                }
                return CharTree::fromArray($charTrees);
            }
        );

        $rule = new GuessableDataRule([], [$formatter]);

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
            static function (iterable $dates): CharTree {
                $charTrees = [];
                foreach ($dates as $date) {
                    $charTrees[] = CharTree::fromString($date->format('m\o\n\t\h'));
                }
                return CharTree::fromArray($charTrees);
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
                'The password cannot contain words that are easy to guess.',
                $password,
                $rule,
                1
            ),
            $rule->validate($password)
        );
    }
}
