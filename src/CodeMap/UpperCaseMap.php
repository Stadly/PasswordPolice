<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use Stadly\PasswordPolice\CodeMap;

final class UpperCaseMap implements CodeMap
{
    /**
     * {@inheritDoc}
     */
    public function getLengths(): array
    {
        return [1];
    }

    /**
     * {@inheritDoc}
     */
    public function code(string $string): array
    {
        return [mb_strtoupper($string)];
    }
}
