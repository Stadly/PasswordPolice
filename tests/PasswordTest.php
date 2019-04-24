<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Password
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class PasswordTest extends TestCase
{
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
        $date = new DateTimeImmutable();
        $password = new Password('foo');
        $password->addGuessableData('bar', $date);

        self::assertEquals(new Password('foo', ['bar', $date]), $password);
    }

    /**
     * @covers ::getGuessableData
     */
    public function testCanGetGuessableData(): void
    {
        $date = new DateTimeImmutable();
        $password = new Password('foo', ['bar', $date]);

        self::assertEquals([
            'bar',
            $date,
        ], $password->getGuessableData(), '', 0, 10, true);
    }

    /**
     * @covers ::clearGuessableData
     */
    public function testCanClearGuessableData(): void
    {
        $password = new Password('foo', ['bar', new DateTimeImmutable()]);
        $password->clearGuessableData();

        self::assertEquals(new Password('foo'), $password);
    }

    /**
     * @covers ::addFormerPasswords
     */
    public function testCanAddFormerPasswords(): void
    {
        $formerPassword1 = new FormerPassword('bar', new DateTimeImmutable('2018-11-29'));
        $formerPassword2 = new FormerPassword('baz', new DateTimeImmutable('2017-01-13'));

        $password = new Password('foo');
        $password->addFormerPasswords($formerPassword1, $formerPassword2);

        self::assertEquals(new Password('foo', [], [$formerPassword1, $formerPassword2]), $password);
    }

    /**
     * @covers ::getFormerPasswords
     */
    public function testCanGetFormerPasswords(): void
    {
        $formerPassword1 = new FormerPassword('bar', new DateTimeImmutable('2018-11-29'));
        $formerPassword2 = new FormerPassword('baz', new DateTimeImmutable('2017-01-13'));

        $password = new Password('foo', [], [$formerPassword1, $formerPassword2]);

        self::assertEquals([
            $formerPassword1,
            $formerPassword2,
        ], $password->getFormerPasswords(), '', 0, 10, true);
    }

    /**
     * @covers ::getFormerPasswords
     */
    public function testFormerPasswordsInConstructorAreOrdered(): void
    {
        $formerPassword2 = new FormerPassword('baz', new DateTimeImmutable('2017-01-13'));
        $formerPassword1 = new FormerPassword('bar', new DateTimeImmutable('2018-11-29'));

        $password = new Password('foo', [], [$formerPassword2, $formerPassword1]);

        self::assertEquals([
            $formerPassword1,
            $formerPassword2,
        ], $password->getFormerPasswords(), '', 0, 10, true);
    }

    /**
     * @covers ::getFormerPasswords
     */
    public function testFormerPasswordsAddedAreOrdered(): void
    {
        $formerPassword2 = new FormerPassword('baz', new DateTimeImmutable('2017-01-13'));
        $formerPassword1 = new FormerPassword('bar', new DateTimeImmutable('2018-11-29'));

        $password = new Password('foo');
        $password->addFormerPasswords($formerPassword2, $formerPassword1);

        self::assertEquals([
            $formerPassword1,
            $formerPassword2,
        ], $password->getFormerPasswords(), '', 0, 10, true);
    }

    /**
     * @covers ::clearFormerPasswords
     */
    public function testCanClearFormerPasswords(): void
    {
        $formerPassword1 = new FormerPassword('bar', new DateTimeImmutable('2018-11-29'));
        $formerPassword2 = new FormerPassword('baz', new DateTimeImmutable('2017-01-13'));

        $password = new Password('foo', [], [$formerPassword1, $formerPassword2]);
        $password->clearFormerPasswords();

        self::assertEquals(new Password('foo'), $password);
    }
}
