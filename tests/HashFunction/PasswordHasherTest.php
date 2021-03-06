<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\HashFunction;

use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\HashFunction\PasswordHasher
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class PasswordHasherTest extends TestCase
{
    /**
     * @covers ::hash
     */
    public function testCanHashPassword(): void
    {
        $hashFunction = new PasswordHasher(PASSWORD_BCRYPT);

        self::assertTrue(password_verify('foo', $hashFunction->hash('foo')));
    }

    /**
     * @covers ::hash
     */
    public function testHashCanThrowException(): void
    {
        $hashFunction = new PasswordHasher(-1);

        $this->expectException(RuntimeException::class);

        $hashFunction->hash('foo');
    }

    /**
     * @covers ::compare
     */
    public function testCanCompareSamePassword(): void
    {
        $hashFunction = new PasswordHasher(PASSWORD_BCRYPT);

        self::assertTrue($hashFunction->compare('foo', '$2y$10$iLpUEjxm56NyXfcYlU8GbOa3aD45x2FEQQEtnbVu4pUWN01.dpxPW'));
    }

    /**
     * @covers ::compare
     */
    public function testCanCompareOtherPassword(): void
    {
        $hashFunction = new PasswordHasher(PASSWORD_BCRYPT);

        self::assertFalse($hashFunction->compare('foo', ''));
    }
}
