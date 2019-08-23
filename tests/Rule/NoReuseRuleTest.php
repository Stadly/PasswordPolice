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
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\NoReuseRule
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
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
            new FormerPassword(new DateTimeImmutable('2006-06-06')),
            new FormerPassword(new DateTimeImmutable('2005-05-05'), 'baz'),
            new FormerPassword(new DateTimeImmutable('2004-04-04'), 'bar'),
            new FormerPassword(new DateTimeImmutable('2003-03-03'), 'foobar'),
            new FormerPassword(new DateTimeImmutable('2002-02-02')),
            new FormerPassword(new DateTimeImmutable('2001-01-01'), 'test'),
        ]);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithCountConstraint(): void
    {
        new NoReuseRule($this->hashFunction, 5, 0);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithFirstConstraint(): void
    {
        new NoReuseRule($this->hashFunction, null, 10);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithBothCountAndFirstConstraint(): void
    {
        new NoReuseRule($this->hashFunction, 5, 10);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithCountConstraintEqualToZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new NoReuseRule($this->hashFunction, 0, 0);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeCountConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new NoReuseRule($this->hashFunction, -10, 0);
    }

    /**
     * @covers ::__construct
     * @doesNotPerformAssertions
     */
    public function testCanConstructRuleWithFirstConstraintEqualToZero(): void
    {
        new NoReuseRule($this->hashFunction, null, 0);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeFirstConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new NoReuseRule($this->hashFunction, null, -5);
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
        $rule1 = new NoReuseRule($this->hashFunction, 5, 5, 1);
        $rule1->addConstraint(10, 10, 2);

        $rule2 = new NoReuseRule($this->hashFunction, 10, 10, 2);
        $rule2->addConstraint(5, 5, 1);
        self::assertEquals($rule1, $rule2);
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

        self::assertNull($rule->validate($this->password, new Translator('en_US')));
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
            $rule->validate($this->password, new Translator('en_US'))
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
            $rule->validate($this->password, new Translator('en_US'))
        );
    }
}
