<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\FormerPassword
 * @covers ::<private>
 * @covers ::<protected>
 * @covers ::__construct
 */
final class FormerPasswordTest extends TestCase
{
    /**
     * @covers ::getHash
     */
    public function testCanGetNullHash(): void
    {
        $date = new DateTimeImmutable('2018-11-28');
        $password = new FormerPassword($date);

        self::assertNull($password->getHash());
    }

    /**
     * @covers ::getHash
     */
    public function testCanGetHash(): void
    {
        $date = new DateTimeImmutable('2018-11-28');
        $password = new FormerPassword($date, 'foo');

        self::assertSame('foo', $password->getHash());
    }

    /**
     * @covers ::getDate
     */
    public function testGetDate(): void
    {
        $date = new DateTimeImmutable('2018-11-28');
        $password = new FormerPassword($date, 'foo');

        self::assertSame($date, $password->getDate());
    }
}
