<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use Stadly\PasswordPolice\CodeMap;

final class MixedCaseMap implements CodeMap
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
        $lowerCase = mb_strtolower($string);
        $upperCase = mb_strtoupper($string);

        $codeMap = [$lowerCase];
        if ($lowerCase !== $upperCase) {
            $codeMap[] = $upperCase;
        }

        return $codeMap;
    }
}
