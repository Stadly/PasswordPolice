<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;

final class ChainingClass
{
    use Chaining;

    /**
     * {@inheritDoc}
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        $charTrees = [];
        foreach ($charTree as $string) {
            $charTrees[] = CharTree::fromString(strrev($string));
        }
        return CharTree::fromArray($charTrees);
    }
}
