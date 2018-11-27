<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordList;

use RuntimeException;

/**
 * Interface that must be implemented by all word lists.
 */
interface WordListInterface
{
    /**
     * Check whether a word is present in the word list.
     *
     * @param string $word Word to check.
     * @return bool Whether the word is present in the word list.
     * @throws RuntimeException If an error occurred while checking the word list.
     */
    public function contains(string $word): bool;
}
