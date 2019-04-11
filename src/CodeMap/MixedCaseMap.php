<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;

final class MixedCaseMap implements CodeMap
{
    /**
     * {@inheritDoc}
     */
    public function getMap(CharTree $charTree): array
    {
        $codeMap = [];
        foreach ($charTree->getTreeTrimmedToLength(1) as $char) {
            $lowerCase = mb_strtolower($char);
            $upperCase = mb_strtoupper($char);

            $codeMap[$char] = [$lowerCase];

            if ($lowerCase !== $upperCase) {
                $codeMap[$char][] = $upperCase;
            }
        }
        return $codeMap;
    }
}
