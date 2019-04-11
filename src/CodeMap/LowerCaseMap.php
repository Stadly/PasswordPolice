<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;

final class LowerCaseMap implements CodeMap
{
    /**
     * {@inheritDoc}
     */
    public function getMap(CharTree $charTree): array
    {
        $codeMap = [];
        foreach ($charTree->getTreeTrimmedToLength(1) as $char) {
            $codeMap[$char] = [mb_strtolower($char)];
        }
        return $codeMap;
    }
}
