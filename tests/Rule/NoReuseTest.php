<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use Stadly\PasswordPolice\FormerPassword;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\HashFunction\HashFunctionInterface;
use Stadly\PasswordPolice\ValidationError;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\NoReuse
 * @covers ::<protected>
 * @covers ::<private>
 */
final class NoReuseTest extends TestCase
{
    /**
     * @var MockObject&HashFunctionInterface
     */
    private $hashFunction;

    /**
     * @var Password
     */
    private $password;

    protected function setUp(): void
    {
        $this->hashFunction = $this->createMock(HashFunctionInterface::class);
        $this->hashFunction->method('compare')->willReturnCallback(
            function ($password, $hash) {
                return $password === $hash;
            }
        );

        $this->password = new Password('foobar', [], [
            new FormerPassword('qwerty', new DateTime('2006-06-06')),
            new FormerPassword('baz', new DateTime('2005-05-05')),
            new FormerPassword('bar', new DateTime('2004-04-04')),
            new FormerPassword('foobar', new DateTime('2003-03-03')),
            new FormerPassword('foo', new DateTime('2002-02-02')),
            new FormerPassword('test', new DateTime('2001-01-01')),
        ]);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithCountConstraint(): void
    {
        $rule = new NoReuse($this->hashFunction, 5, 0);

        // Force generation of code coverage
        $ruleConstruct = new NoReuse($this->hashFunction, 5, 0);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithFirstConstraint(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 10);

        // Force generation of code coverage
        $ruleConstruct = new NoReuse($this->hashFunction, null, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothCountAndFirstConstraint(): void
    {
        $rule = new NoReuse($this->hashFunction, 5, 10);

        // Force generation of code coverage
        $ruleConstruct = new NoReuse($this->hashFunction, 5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithCountConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuse($this->hashFunction, 0, 0);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeCountConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuse($this->hashFunction, -10, 0);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithFirstConstraintEqualToZero(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 0);

        // Force generation of code coverage
        $ruleConstruct = new NoReuse($this->hashFunction, null, 0);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeFirstConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new NoReuse($this->hashFunction, null, -5);
    }

    /**
     * @covers ::getHashFunction
     */
    public function testCanGetHashFunction(): void
    {
        $rule = new NoReuse($this->hashFunction);

        self::assertSame($this->hashFunction, $rule->getHashFunction());
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new NoReuse($this->hashFunction, 5, 5, 1);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = new NoReuse($this->hashFunction, 5, 5, 1);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new NoReuse($this->hashFunction, 5, 5, 1);
        $rule->addConstraint(10, 10, 2);

        $ruleConstruct = new NoReuse($this->hashFunction, 10, 10, 2);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenPasswordIsString(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 0);

        self::assertTrue($rule->test('foobar'));
    }

    /**
     * @covers ::test
     */
    public function testCountConstraintCanBeSatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, 3, 0);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testCountConstraintCanBeUnsatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, 4, 0);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testFirstConstraintCanBeSatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 4);

        self::assertTrue($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testFirstConstraintCanBeUnsatisfied(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 3);

        self::assertFalse($rule->test($this->password));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 3, 1);

        self::assertTrue($rule->test($this->password, 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new NoReuse($this->hashFunction, 1, 0);

        self::assertNull($rule->validate($this->password));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeInvalidated(): void
    {
        $rule = new NoReuse($this->hashFunction, null, 0);

        self::assertEquals(
            new ValidationError($rule, 1, 'Cannot reuse former passwords.'),
            $rule->validate($this->password)
        );
    }
}
