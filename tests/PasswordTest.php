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
     * @covers ::__construct
     */
    public function testCanConstructPasswordWithFormerPasswords(): void
    {
        $formerPassword1 = new FormerPassword('bar', new DateTime('2018-11-29'));
        $formerPassword2 = new FormerPassword('baz', new DateTime('2017-01-13'));

        $password = new Password('foo', [], [$formerPassword1, $formerPassword2]);

        // Force generation of code coverage
        $passwordConstruct = new Password('foo', [], [$formerPassword1, $formerPassword2]);
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

    /**
     * @covers ::addFormerPasswords
     */
    public function testCanAddFormerPasswords(): void
    {
        $formerPassword1 = new FormerPassword('bar', new DateTime('2018-11-29'));
        $formerPassword2 = new FormerPassword('baz', new DateTime('2017-01-13'));

        $password = new Password('foo');
        $password->addFormerPasswords($formerPassword1, $formerPassword2);

        self::assertEquals(new Password('foo', [], [$formerPassword1, $formerPassword2]), $password);
    }

    /**
     * @covers ::getFormerPasswords
     */
    public function testCanGetFormerPasswords(): void
    {
        $formerPassword1 = new FormerPassword('bar', new DateTime('2018-11-29'));
        $formerPassword2 = new FormerPassword('baz', new DateTime('2017-01-13'));

        $password = new Password('foo', [], [$formerPassword1, $formerPassword2]);

        self::assertSame([$formerPassword1, $formerPassword2], $password->getFormerPasswords());
    }

    /**
     * @covers ::getFormerPasswords
     */
    public function testFormerPasswordsInConstructorAreOrdered(): void
    {
        $formerPassword2 = new FormerPassword('baz', new DateTime('2017-01-13'));
        $formerPassword1 = new FormerPassword('bar', new DateTime('2018-11-29'));

        $password = new Password('foo', [], [$formerPassword2, $formerPassword1]);

        self::assertSame([$formerPassword1, $formerPassword2], $password->getFormerPasswords());
    }

    /**
     * @covers ::getFormerPasswords
     */
    public function testFormerPasswordsAddedAreOrdered(): void
    {
        $formerPassword2 = new FormerPassword('baz', new DateTime('2017-01-13'));
        $formerPassword1 = new FormerPassword('bar', new DateTime('2018-11-29'));

        $password = new Password('foo');
        $password->addFormerPasswords($formerPassword2, $formerPassword1);

        self::assertSame([$formerPassword1, $formerPassword2], $password->getFormerPasswords());
    }

    /**
     * @covers ::clearFormerPasswords
     */
    public function testCanClearFormerPasswords(): void
    {
        $formerPassword1 = new FormerPassword('bar', new DateTime('2018-11-29'));
        $formerPassword2 = new FormerPassword('baz', new DateTime('2017-01-13'));

        $password = new Password('foo', [], [$formerPassword1, $formerPassword2]);
        $password->clearFormerPasswords();

        self::assertEquals(new Password('foo'), $password);
    }
}
