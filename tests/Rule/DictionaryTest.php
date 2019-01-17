<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordConverter;
use Stadly\PasswordPolice\WordList;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\Dictionary
 * @covers ::<protected>
 * @covers ::<private>
 */
final class DictionaryTest extends TestCase
{
    /**
     * @var MockObject&WordList
     */
    private $wordList;

    protected function setUp(): void
    {
        $this->wordList = $this->createMock(WordList::class);
        $this->wordList->method('contains')->willReturnCallback(
            static function ($word) {
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
    public function testCanConstructRuleWithMinWordLengthConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, null);

        // Force generation of code coverage
        $ruleConstruct = new Dictionary($this->wordList, 5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxWordLengthConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 1, 10);

        // Force generation of code coverage
        $ruleConstruct = new Dictionary($this->wordList, 1, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinWordLengthAndMaxWordLengthConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, 10);

        // Force generation of code coverage
        $ruleConstruct = new Dictionary($this->wordList, 5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMinWordLengthConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Dictionary($this->wordList, 0, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinWordLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Dictionary($this->wordList, -10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxWordLengthConstraintSmallerThanMinWordLengthConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Dictionary($this->wordList, 10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinWordLengthConstraintEqualToMaxWordLengthConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, 5);

        // Force generation of code coverage
        $ruleConstruct = new Dictionary($this->wordList, 5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::getMinWordLength
     */
    public function testCanGetMinWordLengthConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, 10);

        self::assertSame(5, $rule->getMinWordLength());
    }

    /**
     * @covers ::getMaxWordLength
     */
    public function testCanGetMaxWordLengthConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, 10);

        self::assertSame(10, $rule->getMaxWordLength());
    }

    /**
     * @covers ::getWordList
     */
    public function testCanGetWordList(): void
    {
        $rule = new Dictionary($this->wordList);

        self::assertSame($this->wordList, $rule->getWordList());
    }

    /**
     * @covers ::test
     */
    public function testMinWordLengthConstraintCanBeSatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 3, null);

        self::assertTrue($rule->test('be'));
    }

    /**
     * @covers ::test
     */
    public function testMinWordLengthConstraintCanBeUnsatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 2, null);

        self::assertFalse($rule->test('be'));
    }

    /**
     * @covers ::test
     */
    public function testMaxWordLengthConstraintCanBeSatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 1, 4);

        self::assertTrue($rule->test('apple'));
    }

    /**
     * @covers ::test
     */
    public function testMaxWordLengthConstraintCanBeUnsatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 1, 5);

        self::assertFalse($rule->test('apple'));
    }

    /**
     * @covers ::test
     */
    public function testPrefixWordIsNotRecognized(): void
    {
        $rule = new Dictionary($this->wordList, 1, null);

        self::assertTrue($rule->test('pineapple'));
    }

    /**
     * @covers ::test
     */
    public function testInfixWordIsNotRecognized(): void
    {
        $rule = new Dictionary($this->wordList, 1, null);

        self::assertTrue($rule->test('pineapplejack'));
    }

    /**
     * @covers ::test
     */
    public function testPostfixWordIsNotRecognized(): void
    {
        $rule = new Dictionary($this->wordList, 1, null);

        self::assertTrue($rule->test('applejack'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new Dictionary($this->wordList, 1, 5, [], 1);

        self::assertTrue($rule->test('apple', 2));
    }

    /**
     * @covers ::test
     */
    public function testTestThrowsExceptionWhenWordListThrowsException(): void
    {
        $wordList = $this->createMock(WordList::class);
        $wordList->method('contains')->willThrowException(new RuntimeException());

        $rule = new Dictionary($wordList);

        $this->expectException(Exception::class);

        $rule->test('foo');
    }

    /**
     * @covers ::test
     */
    public function testWordIsRecognizedAfterSingleWordConverter(): void
    {
        $wordConverter = $this->createMock(WordConverter::class);
        $wordConverter->method('convert')->willReturnCallback(
            static function ($word) {
                yield str_replace(['4', '€'], ['a', 'e'], $word);
            }
        );

        $rule = new Dictionary($this->wordList, 1, null, [$wordConverter]);

        self::assertFalse($rule->test('4ppl€'));
        self::assertTrue($rule->test('pine4ppl€jack'));
    }

    /**
     * @covers ::test
     */
    public function testWordIsRecognizedAfterMultipleWordConverters(): void
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

        $rule = new Dictionary($this->wordList, 1, null, [$wordConverter1, $wordConverter2]);

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
        $rule = new Dictionary($this->wordList, 1, null);

        self::assertNull($rule->validate('foo'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeInvalidated(): void
    {
        $rule = new Dictionary($this->wordList, 1, null);

        self::assertEquals(
            new ValidationError('Must not contain dictionary words.', 'apple', $rule, 1),
            $rule->validate('apple')
        );
    }
}
