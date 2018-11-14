<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Translator;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\UpperCaseException
 * @covers ::<protected>
 * @covers ::<private>
 */
final class UpperCaseExceptionTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMinConstraint(): void
    {
        $translator = new Translator('en_EN');
        $exception = new UpperCaseException(new UpperCase(5), 3, $translator);

        // Force generation of code coverage
        self::assertSame('There must be at least 5 upper case characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithMaxConstraint(): void
    {
        $translator = new Translator('en_EN');
        $exception = new UpperCaseException(new UpperCase(0, 10), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be at most 10 upper case characters.', $exception->getMessage());
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructExceptionWithBothMinAndMaxConstraint(): void
    {
        $translator = new Translator('en_EN');
        $exception = new UpperCaseException(new UpperCase(5, 10), 15, $translator);

        // Force generation of code coverage
        self::assertSame('There must be between 5 and 10 upper case characters.', $exception->getMessage());
    }

    /**
     * @covers ::getCount
     */
    public function testCanGetCount(): void
    {
        $translator = new Translator('en_EN');
        $exception = new UpperCaseException(new UpperCase(5, 10), 15, $translator);

        self::assertSame(15, $exception->getCount());
    }
}
