<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Stadly\PasswordPolice\WordList\WordListInterface;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\Dictionary
 * @covers ::<protected>
 * @covers ::<private>
 */
final class DictionaryTest extends TestCase
{
    /**
     * @var MockObject&WordListInterface
     */
    private $wordList;

    protected function setUp(): void
    {
        $this->wordList = $this->createMock(WordListInterface::class);
        $this->wordList->method('contains')->willReturnCallback(
            function ($word) {
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
    public function testMinWordLengthConstraintCanBeSatisfiedWhenCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 3, null, true);

        self::assertTrue($rule->test('be'));
    }

    /**
     * @covers ::test
     */
    public function testMinWordLengthConstraintCanBeSatisfiedWhenNotCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 3, null, false);

        self::assertTrue($rule->test('be'));
    }

    /**
     * @covers ::test
     */
    public function testMinWordLengthConstraintCanBeUnsatisfiedWhenCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 2, null, true);

        self::assertFalse($rule->test('be'));
    }

    /**
     * @covers ::test
     */
    public function testMinWordLengthConstraintCanBeUnsatisfiedWhenNotCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 2, null, false);

        self::assertFalse($rule->test('be'));
    }

    /**
     * @covers ::test
     */
    public function testMaxWordLengthConstraintCanBeSatisfiedWhenCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, 4, true);

        self::assertTrue($rule->test('apple'));
    }

    /**
     * @covers ::test
     */
    public function testMaxWordLengthConstraintCanBeSatisfiedWhenNotCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, 4, false);

        self::assertTrue($rule->test('apple'));
    }

    /**
     * @covers ::test
     */
    public function testMaxWordLengthConstraintCanBeUnsatisfiedWhenCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, 5, true);

        self::assertFalse($rule->test('apple'));
    }

    /**
     * @covers ::test
     */
    public function testMaxWordLengthConstraintCanBeUnsatisfiedWhenNotCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, 5, false);

        self::assertFalse($rule->test('apple'));
    }

    /**
     * @covers ::test
     */
    public function testPrefixWordIsRecognizedWhenCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, true);

        self::assertFalse($rule->test('pineapple'));
    }

    /**
     * @covers ::test
     */
    public function testPrefixWordIsNotRecognizedWhenNotCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, false);

        self::assertTrue($rule->test('pineapple'));
    }

    /**
     * @covers ::test
     */
    public function testInfixWordIsRecognizedWhenCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, true);

        self::assertFalse($rule->test('pineapplejack'));
    }

    /**
     * @covers ::test
     */
    public function testInfixWordIsNotRecognizedWhenNotCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, false);

        self::assertTrue($rule->test('pineapplejack'));
    }

    /**
     * @covers ::test
     */
    public function testPostfixWordIsRecognizedWhenCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, true);

        self::assertFalse($rule->test('applejack'));
    }

    /**
     * @covers ::test
     */
    public function testPostfixWordIsNotRecognizedWhenNotCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, false);

        self::assertTrue($rule->test('applejack'));
    }

    /**
     * @covers ::test
     */
    public function testTestThrowsExceptionWhenWordListThrowsException(): void
    {
        $wordList = $this->createMock(WordListInterface::class);
        $wordList->method('contains')->willThrowException(new RuntimeException());

        $rule = new Dictionary($wordList);

        $this->expectException(TestException::class);

        $rule->test('foo');
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceDoesNotThrowExceptionWhenRuleIsSatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 1, null);

        $rule->enforce('foo');

        // Force generation of code coverage
        $ruleConstruct = new Dictionary($this->wordList, 1, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::enforce
     */
    public function testEnforceThrowsExceptionWhenRuleIsNotSatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 1, null);

        $this->expectException(RuleException::class);

        $rule->enforce('apple');
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageWhenCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, true);

        self::assertSame('Must not contain dictionary words.', $rule->getMessage());
    }

    /**
     * @covers ::getMessage
     */
    public function testCanGetMessageWhenNotCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, false);

        self::assertSame('Must not be a dictionary word.', $rule->getMessage());
    }
}
