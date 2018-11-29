<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeInterface;

final class FormerPassword
{
    /**
     * @var string Password hash.
     */
    private $hash;

    /**
     * @var DateTimeInterface Creation date.
     */
    private $date;

    /**
     * @param string $hash Password hash.
     * @param DateTimeInterface $date Creation date.
     */
    public function __construct(string $hash, DateTimeInterface $date)
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
     * @return DateTimeInterface Creation date.
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
