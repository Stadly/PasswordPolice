<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeInterface;
use StableSort\StableSort;

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
     * @var FormerPassword[] Former passwords, ordered by recentness.
     */
    private $formerPasswords = [];

    /**
     * @param string $password Password.
     * @param (string|DateTimeInterface)[] $guessableData Guessable data.
     * @param FormerPassword[] $formerPasswords Former passwords.
     */
    public function __construct(string $password, array $guessableData = [], array $formerPasswords = [])
    {
        $this->password = $password;
        $this->guessableData = $guessableData;
        $this->addFormerPasswords(...$formerPasswords);
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
     * @param string|DateTimeInterface ...$guessableData Guessable data.
     */
    public function addGuessableData(...$guessableData): void
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

    /**
     * @param FormerPassword...$formerPasswords Former passwords.
     */
    public function addFormerPasswords(FormerPassword ...$formerPasswords): void
    {
        $this->formerPasswords = array_merge($this->formerPasswords, $formerPasswords);

        StableSort::usort($this->formerPasswords, static function (FormerPassword $a, FormerPassword $b): int {
            return $b->getDate() <=> $a->getDate();
        });
    }

    /**
     * @return FormerPassword[] Former passwords, ordered by recentness.
     */
    public function getFormerPasswords(): array
    {
        return $this->formerPasswords;
    }

    public function clearFormerPasswords(): void
    {
        $this->formerPasswords = [];
    }
}
