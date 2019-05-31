<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

/**
 * Interface that must be implemented by all code maps.
 */
interface CodeMap
{
    /**
     * @return array<int> Distinct lengths of entries in the code map.
     */
    public function getLengths(): array;

    /**
     * @param string $string String to code.
     * @return array<string> Coded variants of the string.
     */
    public function code(string $string): array;
}
