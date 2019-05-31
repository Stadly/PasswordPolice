<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CharTree;

use InvalidArgumentException;
use Stadly\PasswordPolice\CharTree;

final class Cutter
{
    /**
     * @var array<array<array<string|CharTree>>> Memoization of cut character trees.
     */
    private static $memoization = [];

    /**
     * @param CharTree $charTree Character tree to cut.
     * @param int $position Number of characters before cut.
     * @return array<array<string|CharTree>> Cut variants of the character tree.
     */
    public function cut(CharTree $charTree, int $position): array
    {
        if ($position < 0) {
            throw new InvalidArgumentException('Position must be non-negative.');
        }

        // When PHP 7.1 is no longer supported, change to using spl_object_id.
        $hash = spl_object_hash($charTree).';'.$position;

        if (!isset(self::$memoization[$hash])) {
            self::$memoization[$hash] = $this->cutInternal($charTree, $position);
        }

        return self::$memoization[$hash];
    }

    /**
     * @param CharTree $charTree Character tree to cut.
     * @param int $position Number of characters before cut.
     * @return array<array<string|CharTree>> Cut variants of the character tree. Memoization is not used.
     */
    private function cutInternal(CharTree $charTree, int $position): array
    {
        $root = $charTree->getRoot();

        if ($root === null) {
            return [];
        }

        $rootLength = mb_strlen($root);
        $branchPosition = $position-$rootLength;

        if ($branchPosition < 0) {
            return [['', $charTree]];
        }

        if ($branchPosition === 0) {
            return [[$root, CharTree::fromArray($charTree->getBranches())]];
        }

        $cutCharTrees = [];
        foreach ($charTree->getBranches() as $branch) {
            foreach ($this->cut($branch, $branchPosition) as [$branchRoot, $branchTree]) {
                assert(is_string($branchRoot));
                assert(is_object($branchTree));

                $cutCharTrees[] = [$root.$branchRoot, $branchTree];
            }
        }

        return $cutCharTrees;
    }
}
