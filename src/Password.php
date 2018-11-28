<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeInterface;

final class Password
{
    /**
     * @var string Password.
     */
    private $password;

    /**
     * @var (string|DateTimeInterface)[] Guessable data.
     */
    private $guessableData;

    /**
     * @param string $password Password.
     * @param (string|DateTimeInterface)[] $guessableData Guessable data.
     */
    public function __construct(string $password, array $guessableData = [])
    {
        $this->password = $password;
        $this->guessableData = $guessableData;
    }

    /**
     * @return string Password.
     */
    public function __toString(): string
    {
        return $this->password;
    }

    /**
     * @return string Password.
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return (string|DateTimeInterface)[]
     */
    public function getGuessableData(): array
    {
        return $this->guessableData;
    }
}
