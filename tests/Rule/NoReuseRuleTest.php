<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\HashFunction;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\ValidationError;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\NoReuseRule
 * @covers ::<protected>
 * @covers ::<private>
 */
final class NoReuseRuleTest extends TestCase
{
    /**
     * @var MockObject&HashFunction
     */
    private $hashFunction;

    /**
     * @var Password
     */
    private $password;

    protected function setUp(): void
    {
        $this->hashFunction = $this->createMock(HashFunction::class);
        $this->hashFunction->method('compare')->willReturnCallback(
            static function (string $password, string $hash): bool {
                return $password === $hash;
            }
        );

        $this->password = new Password('foobar', [], [
            new FormerPassword('qwerty', new DateTimeImmutable('2006-06-06')),
            new FormerPassword('baz', new DateTimeImmutable('2005-05-05')),
            new FormerPassword('bar', new DateTimeImmutable('2004-04-04')),
            new FormerPassword('foobar', new DateTimeImmutable('2003-03-03')),
            new FormerPassword('foo', new DateTimeImmutable('2002-02-02')),
            new FormerPassword('test', new DateTimeImmutable('2001-01-01')),
        ]);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithCountConstraint(): void
    {
        $rule = new NoReuseRule($this->hashFunction, 5, 0);

        // Force generation of code coverage
        $ruleConstruct = new NoReuseRule($this->hashFunction, 5, 0);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithFirstConstraint(): void
    {
        $rule = new NoReuseRule($this->hashFunction, null, 10);

        // Force generation of code coverage
        $ruleConstruct = new NoReuseRule($this->hashFunction, null, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothCountAndFirstConstraint(): void
    {
        $rule = new NoReuseRule($this->hashFunction, 5, 10);

        // Force generation of code coverage
        $ruleConstruct = new NoReuseRule($this->hashFunction, 5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithCountConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuseRule($this->hashFunction, 0, 0);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeCountConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuseRule($this->hashFunction, -10, 0);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithFirstConstraintEqualToZero(): void
    {
        $rule = new NoReuseRule($this->hashFunction, null, 0);

        // Force generation of code coverage
        $ruleConstruct = new NoReuseRule($this->hashFunction, null, 0);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeFirstConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuseRule($this->hashFunction, null, -5);
    }

    /**
     * @covers ::getHashFunction
     */
    public function testCanGetHashFunction(): void
    {
        $rule = new NoReuseRule($this->hashFunction);

        self::assertSame($this->hashFunction, $rule->getHashFunction());
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new NoReuseRule($this->hashFunction, 5, 5, 1);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = new NoReuseRule($this->hashFunction, 5, 5, 1);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new NoReuseRule($this->hashFunction, 5, 5, 1);
        $rule->addConstraint(10, 10, 2);

        $ruleConstruct = new NoReuseRule($this->hashFunction, 10, 10, 2);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new NoReuseRule($this->hashFunction, null, 0);

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testCountConstraintCanBeSatisfied(): void
    {
        $rule = new NoReuseRule($this->hashFunction, 3, 0);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testCountConstraintCanBeUnsatisfied(): void
    {
        $rule = new NoReuseRule($this->hashFunction, 4, 0);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testFirstConstraintCanBeSatisfied(): void
    {
        $rule = new NoReuseRule($this->hashFunction, null, 4);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testFirstConstraintCanBeUnsatisfied(): void
    {
        $rule = new NoReuseRule($this->hashFunction, null, 3);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new NoReuseRule($this->hashFunction, null, 3, 1);

        self::assertTrue($rule->test($this->password, 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new NoReuseRule($this->hashFunction, 1, 0);

        self::assertNull($rule->validate($this->password));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithCountContraintCanBeInvalidated(): void
    {
        $rule = new NoReuseRule($this->hashFunction, 5, 0);

        self::assertEquals(
            new ValidationError(
                'The 5 most recently used passwords cannot be reused.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password)
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithoutCountContraintCanBeInvalidated(): void
    {
        $rule = new NoReuseRule($this->hashFunction, null, 0);

        self::assertEquals(
            new ValidationError(
                'Formerly used passwords cannot be reused.',
                $this->password,
                $rule,
                1
            ),
            $rule->validate($this->password)
        );
    }
}
