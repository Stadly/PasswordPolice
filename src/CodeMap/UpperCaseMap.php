<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;

final class UpperCaseMap implements CodeMap
{
    /**
     * {@inheritDoc}
     */
    public function getMap(CharTree $charTree): array
    {
        $codeMap = [];
        foreach ($charTree->getTreeTrimmedToLength(1) as $char) {
            $codeMap[$char] = [mb_strtoupper($char)];
        }
        return $codeMap;
    }
}
