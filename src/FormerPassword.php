<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeImmutable;

final class FormerPassword
{
    /**
     * @var string Password hash.
     */
    private $hash;

    /**
     * @var DateTimeImmutable Creation date.
     */
    private $date;

    /**
     * @param string $hash Password hash.
     * @param DateTimeImmutable $date Creation date.
     */
    public function __construct(string $hash, DateTimeImmutable $date)
    {
        $this->hash = $hash;
        $this->date = $date;
    }

    /**
     * @return string Password hash.
     */
    public function __toString(): string
    {
        return $this->hash;
    }

    /**
     * @return string Password hash.
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return DateTimeImmutable Creation date.
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
