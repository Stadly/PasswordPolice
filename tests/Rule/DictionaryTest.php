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
        $this->wordList->method('contains')->will(self::returnCallback(
            function ($word) {
                switch ($word) {
                    case 'apple':
                    case 'be':
                        return true;
                    default:
                        return false;
                }
            }
        ));
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, null);

        // Force generation of code coverage
        $ruleConstruct = new Dictionary($this->wordList, 5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 1, 10);

        // Force generation of code coverage
        $ruleConstruct = new Dictionary($this->wordList, 1, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, 10);

        // Force generation of code coverage
        $ruleConstruct = new Dictionary($this->wordList, 5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMinConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Dictionary($this->wordList, 0, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Dictionary($this->wordList, -10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new Dictionary($this->wordList, 10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, 5);

        // Force generation of code coverage
        $ruleConstruct = new Dictionary($this->wordList, 5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::getMin
     */
    public function testCanGetMinConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, 10);

        self::assertSame(5, $rule->getMin());
    }

    /**
     * @covers ::getMax
     */
    public function testCanGetMaxConstraint(): void
    {
        $rule = new Dictionary($this->wordList, 5, 10);

        self::assertSame(10, $rule->getMax());
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
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 3, null);

        self::assertTrue($rule->test('be'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 2, null);

        self::assertFalse($rule->test('be'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 1, 4);

        self::assertTrue($rule->test('apple'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new Dictionary($this->wordList, 1, 5);

        self::assertFalse($rule->test('apple'));
    }

    /**
     * @covers ::test
     */
    public function testPrefixWordIsRecognized(): void
    {
        $rule = new Dictionary($this->wordList, 1, null);

        self::assertFalse($rule->test('pineapple'));
    }

    /**
     * @covers ::test
     */
    public function testInfixWordIsRecognized(): void
    {
        $rule = new Dictionary($this->wordList, 1, null);

        self::assertFalse($rule->test('pineapplejack'));
    }

    /**
     * @covers ::test
     */
    public function testPostfixWordIsRecognized(): void
    {
        $rule = new Dictionary($this->wordList, 1, null);

        self::assertFalse($rule->test('applejack'));
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
    public function testCanGetMessage(): void
    {
        $rule = new Dictionary($this->wordList);

        self::assertSame('Must not contain common dictionary words.', $rule->getMessage());
    }
}
