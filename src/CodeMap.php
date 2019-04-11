<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

/**
 * Interface that must be implemented by all code maps.
 */
interface CodeMap
{
    /**
     * @param CharTree $charTree Character tree to get code map for.
     * @return array<string|int, string[]> Map for coding the root of the character tree.
     */
    public function getMap(CharTree $charTree): array;
}