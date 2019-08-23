<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeImmutable;

final class FormerPassword
{
    /**
     * @var DateTimeImmutable Creation date.
     */
    private $date;

    /**
     * @var string|null Password hash.
     */
    private $hash;

    /**
     * @param DateTimeImmutable $date Creation date.
     * @param string $hash Password hash.
     */
    public function __construct(DateTimeImmutable $date, ?string $hash = null)
    {
        $this->date = $date;
        $this->hash = $hash;
    }

    /**
     * @return DateTimeImmutable Creation date.
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string|null Password hash.
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }
}
