<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use Stadly\PasswordPolice\CharTree;

final class ChainingClass
{
    use Chaining;

    /**
     * @inheritDoc
     */
    protected function applyCurrent(iterable $dates): CharTree
    {
        $charTrees = [];
        foreach ($dates as $date) {
            $charTrees[] = CharTree::fromString($date->format('d/m/Y'));
        }
        return CharTree::fromArray($charTrees);
    }
}
