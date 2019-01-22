<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordFormatter;
use Stadly\PasswordPolice\WordList;
use Traversable;

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

        $this->expectException(Exception::class);

        $rule->test('foo');
    }

    /**
     * @covers ::test
     */
    public function testWordIsRecognizedAfterSingleWordFormatter(): void
    {
        $wordFormatter = $this->createMock(WordFormatter::class);
        $wordFormatter->method('apply')->willReturnCallback(
            static function (iterable $words): Traversable {
                foreach ($words as $word) {
                    yield str_replace(['4', '€'], ['a', 'e'], $word);
                }
            }
        );

        $rule = new DictionaryRule($this->wordList, [$wordFormatter]);

        self::assertFalse($rule->test('4ppl€'));
        self::assertTrue($rule->test('pine4ppl€jack'));
    }

    /**
     * @covers ::test
     */
    public function testWordIsRecognizedAfterMultipleWordFormatters(): void
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

        $rule = new DictionaryRule($this->wordList, [$wordFormatter1, $wordFormatter2]);

        self::assertTrue($rule->test('4ppl€'));
        self::assertTrue($rule->test('pine4ppl€jack'));
        self::assertFalse($rule->test('4pple'));
        self::assertTrue($rule->test('pine4pplejack'));
        self::assertFalse($rule->test('appl€'));
        self::assertTrue($rule->test('pineappl€jack'));
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
            new ValidationError('Must not contain dictionary words.', 'apple', $rule, 1),
            $rule->validate('apple')
        );
    }
}
