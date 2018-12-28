<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\HashFunction;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Notice;
use RuntimeException;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\HashFunction\PasswordHash
 * @covers ::<protected>
 * @covers ::<private>
 */
final class PasswordHashTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructHashFunction(): void
    {
        $hashFunction = new PasswordHash(PASSWORD_BCRYPT);

        // Force generation of code coverage
        $hashFunctionConstruct = new PasswordHash(PASSWORD_BCRYPT);
        self::assertEquals($hashFunction, $hashFunctionConstruct);
    }

    /**
     * @covers ::hash
     */
    public function testCanHashPassword(): void
    {
        $hashFunction = new PasswordHash(PASSWORD_BCRYPT);

        self::assertTrue(password_verify('foo', $hashFunction->hash('foo')));
    }

    /**
     * @covers ::hash
     */
    public function testHashCanThrowException(): void
    {
        $hashFunction = new PasswordHash(-1);

        $this->expectException(RuntimeException::class);

        $hashFunction->hash('foo');
    }

    /**
     * @covers ::compare
     */
    public function testCanCompareSamePassword(): void
    {
        $hashFunction = new PasswordHash(PASSWORD_BCRYPT);

        self::assertTrue($hashFunction->compare('foo', '$2y$10$iLpUEjxm56NyXfcYlU8GbOa3aD45x2FEQQEtnbVu4pUWN01.dpxPW'));
    }

    /**
     * @covers ::compare
     */
    public function testCanCompareOtherPassword(): void
    {
        $hashFunction = new PasswordHash(PASSWORD_BCRYPT);

        self::assertFalse($hashFunction->compare('foo', ''));
    }
}
