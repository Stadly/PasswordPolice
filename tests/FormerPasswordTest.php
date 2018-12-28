<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\FormerPassword
 * @covers ::<protected>
 * @covers ::<private>
 */
final class FormerPasswordTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructFormerPassword(): void
    {
        $date = new DateTimeImmutable('2018-11-28');
        $password = new FormerPassword('foo', $date);

        // Force generation of code coverage
        $passwordConstruct = new FormerPassword('foo', $date);
        self::assertEquals($password, $passwordConstruct);
    }

    /**
     * @covers ::__toString
     */
    public function testCanConvertToString(): void
    {
        $date = new DateTimeImmutable('2018-11-28');
        $password = new FormerPassword('foo', $date);

        self::assertSame('foo', (string)$password);
    }

    /**
     * @covers ::getHash
     */
    public function testCanGetHash(): void
    {
        $date = new DateTimeImmutable('2018-11-28');
        $password = new FormerPassword('foo', $date);

        self::assertSame('foo', $password->getHash());
    }

    /**
     * @covers ::getDate
     */
    public function testGetDate(): void
    {
        $date = new DateTimeImmutable('2018-11-28');
        $password = new FormerPassword('foo', $date);

        self::assertSame($date, $password->getDate());
    }
}
