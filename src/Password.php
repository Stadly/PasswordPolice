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
     * @param string|DateTimeInterface... $guessableData Guessable data.
     */
    public function addGuessableData(... $guessableData): void
    {
        $this->guessableData = array_merge($this->guessableData, $guessableData);
    }

    /**
     * @return (string|DateTimeInterface)[] Guessable data.
     */
    public function getGuessableData(): array
    {
        return $this->guessableData;
    }

    public function clearGuessableData(): void
    {
        $this->guessableData = [];
    }
}
