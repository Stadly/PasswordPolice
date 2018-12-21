<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;
use Stadly\PasswordPolice\ValidationError;
use Stadly\PasswordPolice\WordConverter\WordConverterInterface;
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
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new Dictionary($this->wordList, 1, 5, false, [], 1);

        self::assertTrue($rule->test('apple', 2));
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
     * @covers ::test
     */
    public function testWholeWordIsRecognizedAfterSingleWordConverter(): void
    {
        $wordConverter = $this->createMock(WordConverterInterface::class);
        $wordConverter->method('convert')->willReturnCallback(
            function ($word) {
                yield str_replace(['4', '€'], ['a', 'e'], $word);
            }
        );

        $rule = new Dictionary($this->wordList, 1, null, false, [$wordConverter]);

        self::assertFalse($rule->test('4ppl€'));
        self::assertTrue($rule->test('pine4ppl€jack'));
    }

    /**
     * @covers ::test
     */
    public function testSubstringWordIsRecognizedAfterSingleWordConverter(): void
    {
        $wordConverter = $this->createMock(WordConverterInterface::class);
        $wordConverter->method('convert')->willReturnCallback(
            function ($word) {
                yield str_replace(['4', '€'], ['a', 'e'], $word);
            }
        );

        $rule = new Dictionary($this->wordList, 1, null, true, [$wordConverter]);

        self::assertFalse($rule->test('4ppl€'));
        self::assertFalse($rule->test('pine4ppl€jack'));
    }

    /**
     * @covers ::test
     */
    public function testWholeWordIsRecognizedAfterMultipleWordConverters(): void
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

        $rule = new Dictionary($this->wordList, 1, null, false, [$wordConverter1, $wordConverter2]);

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
    public function testSubstringWordIsRecognizedAfterMultipleWordConverters(): void
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

        $rule = new Dictionary($this->wordList, 1, null, true, [$wordConverter1, $wordConverter2]);

        self::assertTrue($rule->test('4ppl€'));
        self::assertTrue($rule->test('pine4ppl€jack'));
        self::assertFalse($rule->test('4pple'));
        self::assertFalse($rule->test('pine4pplejack'));
        self::assertFalse($rule->test('appl€'));
        self::assertFalse($rule->test('pineappl€jack'));
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
    public function testRuleCanBeInvalidatedWhenCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, true);

        self::assertEquals(
            new ValidationError('Must not contain dictionary words.', 'apple', $rule, 1),
            $rule->validate('apple')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeInvalidatedWhenNotCheckingSubstrings(): void
    {
        $rule = new Dictionary($this->wordList, 1, null, false);

        self::assertEquals(
            new ValidationError('Must not be a dictionary word.', 'apple', $rule, 1),
            $rule->validate('apple')
        );
    }
}
