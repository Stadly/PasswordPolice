<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Password
 * @covers ::<protected>
 * @covers ::<private>
 */
final class PasswordTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructPassword(): void
    {
        $password = new Password('foo');

        // Force generation of code coverage
        $passwordConstruct = new Password('foo');
        self::assertEquals($password, $passwordConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructPasswordWithGuessableData(): void
    {
        $date = new DateTime();
        $password = new Password('foo', ['bar', $date]);

        // Force generation of code coverage
        $passwordConstruct = new Password('foo', ['bar', $date]);
        self::assertEquals($password, $passwordConstruct);
    }

    /**
     * @covers ::__toString
     */
    public function testCanConvertToString(): void
    {
        $password = new Password('baz');

        self::assertSame('baz', (string)$password);
    }

    /**
     * @covers ::getPassword
     */
    public function testCanGetPassword(): void
    {
        $password = new Password('bar');

        self::assertSame('bar', $password->getPassword());
    }

    /**
     * @covers ::addGuessableData
     */
    public function testCanAddGuessableData(): void
    {
        $date = new DateTime();
        $password = new Password('foo');
        $password->addGuessableData('bar', $date);

        self::assertEquals(new Password('foo', ['bar', $date]), $password);
    }

    /**
     * @covers ::getGuessableData
     */
    public function testCanGetGuessableData(): void
    {
        $date = new DateTime();
        $password = new Password('foo', ['bar', $date]);

        self::assertSame(['bar', $date], $password->getGuessableData());
    }

    /**
     * @covers ::clearGuessableData
     */
    public function testCanClearGuessableData(): void
    {
        $password = new Password('foo', ['bar', new DateTime()]);
        $password->clearGuessableData();

        self::assertEquals(new Password('foo'), $password);
    }
}
