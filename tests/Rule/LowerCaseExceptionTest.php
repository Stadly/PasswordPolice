<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\LowerCaseException
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LowerCaseExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMinConstraint(): void
    {
        $translator = new Translator('en_US');
        $exception = new LowerCaseException(new LowerCase(5), 3, $translator);

        // Force generation of code coverage
        self::assertSame('There must be at least 5 lower case characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $exception = new LowerCaseException(new LowerCase(0, 10), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be at most 10 lower case characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithBothMinAndMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $exception = new LowerCaseException(new LowerCase(5, 10), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be between 5 and 10 lower case characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMaxConstraintEqualToZero(): void
    {
        $translator = new Translator('en_US');
        $exception = new LowerCaseException(new LowerCase(0, 0), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be no lower case characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMinConstraintEqualToMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $exception = new LowerCaseException(new LowerCase(3, 3), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be exactly 3 lower case characters.', $exception->getMessage());
    }

    /**
     * @covers ::getCount
     */
    public function testCanGetCount(): void
    {
        $translator = new Translator('en_US');
        $exception = new LowerCaseException(new LowerCase(5, 10), 15, $translator);

        self::assertSame(15, $exception->getCount());
    }
}
