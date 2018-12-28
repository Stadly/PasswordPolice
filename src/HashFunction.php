<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use RuntimeException;

/**
 * Interface that must be implemented by all hash functions.
 */
interface HashFunction
{
    /**
     * @param string $password Password to hash.
     * @return string Hashed password.
     * @throws RuntimeException If an error occurred while hashing.
     */
    public function hash(string $password): string;

    /**
     * @param string $password Password.
     * @param string $hash Hash.
     * @return bool Whether the hash matches the password.
     */
    public function compare(string $password, string $hash): bool;
}
