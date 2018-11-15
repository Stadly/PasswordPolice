<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\LengthException
 * @covers ::<protected>
 * @covers ::<private>
 */
final class LengthExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMinConstraint(): void
    {
        $translator = new Translator('en_US');
        $exception = new LengthException(new Length(5), 3, $translator);

        // Force generation of code coverage
        self::assertSame('There must be at least 5 characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $exception = new LengthException(new Length(0, 10), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be at most 10 characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithBothMinAndMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $exception = new LengthException(new Length(5, 10), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be between 5 and 10 characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMaxConstraintEqualToZero(): void
    {
        $translator = new Translator('en_US');
        $exception = new LengthException(new Length(0, 0), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be no characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMinConstraintEqualToMaxConstraint(): void
    {
        $translator = new Translator('en_US');
        $exception = new LengthException(new Length(3, 3), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be exactly 3 characters.', $exception->getMessage());
    }

    /**
     * @covers ::getCount
     */
    public function testCanGetCount(): void
    {
        $translator = new Translator('en_US');
        $exception = new LengthException(new Length(5, 10), 15, $translator);

        self::assertSame(15, $exception->getCount());
    }
}
