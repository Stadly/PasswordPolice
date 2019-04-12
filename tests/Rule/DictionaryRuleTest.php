<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordList;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\DictionaryRule
 * @covers ::<protected>
 * @covers ::<private>
 */
final class DictionaryRuleTest extends TestCase
{
    /**
     * @var MockObject&WordList
     */
    private $wordList;

    protected function setUp(): void
    {
        $this->wordList = $this->createMock(WordList::class);
        $this->wordList->method('contains')->willReturnCallback(
            static function (string $word): bool {
                switch ($word) {
                    case 'apple':
                    case 'be':
                        return true;
                    default:
                        return false;
                }
            }
        );
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRule(): void
    {
        $rule = new DictionaryRule($this->wordList);

        // Force generation of code coverage
        $ruleConstruct = new DictionaryRule($this->wordList);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::getWordList
     */
    public function testCanGetWordList(): void
    {
        $rule = new DictionaryRule($this->wordList);

        self::assertSame($this->wordList, $rule->getWordList());
    }

    /**
     * @covers ::test
     */
    public function testRuleCanBeSatisfied(): void
    {
        $rule = new DictionaryRule($this->wordList);

        self::assertTrue($rule->test('test'));
    }

    /**
     * @covers ::test
     */
    public function testRuleCanBeUnsatisfied(): void
    {
        $rule = new DictionaryRule($this->wordList);

        self::assertFalse($rule->test('apple'));
    }

    /**
     * @covers ::test
     */
    public function testPrefixWordIsNotRecognized(): void
    {
        $rule = new DictionaryRule($this->wordList);

        self::assertTrue($rule->test('applejack'));
    }

    /**
     * @covers ::test
     */
    public function testInfixWordIsNotRecognized(): void
    {
        $rule = new DictionaryRule($this->wordList);

        self::assertTrue($rule->test('pineapplejack'));
    }

    /**
     * @covers ::test
     */
    public function testPostfixWordIsNotRecognized(): void
    {
        $rule = new DictionaryRule($this->wordList);

        self::assertTrue($rule->test('pineapple'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new DictionaryRule($this->wordList, [], 1);

        self::assertTrue($rule->test('apple', 2));
    }

    /**
     * @covers ::test
     */
    public function testTestThrowsExceptionWhenWordListThrowsException(): void
    {
        $wordList = $this->createMock(WordList::class);
        $wordList->method('contains')->willThrowException(new RuntimeException());

        $rule = new DictionaryRule($wordList);

        $this->expectException(RuleException::class);

        $rule->test('foo');
    }

    /**
     * @covers ::test
     */
    public function testWordIsRecognizedAfterSingleFormatter(): void
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

        $rule = new DictionaryRule($this->wordList, [$formatter]);

        self::assertFalse($rule->test('4ppl€'));
        self::assertTrue($rule->test('pine4ppl€jack'));
    }

    /**
     * @covers ::test
     */
    public function testWordIsRecognizedAfterMultipleFormatters(): void
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

        $rule = new DictionaryRule($this->wordList, [$formatter1, $formatter2]);

        self::assertTrue($rule->test('4ppl€'));
        self::assertTrue($rule->test('pine4ppl€jack'));
        self::assertFalse($rule->test('4pple'));
        self::assertTrue($rule->test('pine4pplejack'));
        self::assertFalse($rule->test('appl€'));
        self::assertTrue($rule->test('pineappl€jack'));
    }

    /**
     * @covers ::test
     */
    public function testUnformattedWordIsRecognizedAfterFormatter(): void
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

        $rule = new DictionaryRule($this->wordList, [$formatter]);

        self::assertFalse($rule->test('apple'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new DictionaryRule($this->wordList);

        self::assertNull($rule->validate('foo'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeInvalidated(): void
    {
        $rule = new DictionaryRule($this->wordList);

        self::assertEquals(
            new ValidationError(
                'The password cannot contain dictionary words.',
                'apple',
                $rule,
                1
            ),
            $rule->validate('apple')
        );
    }
}
