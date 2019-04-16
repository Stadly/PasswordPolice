<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;
use Stadly\PasswordPolice\Formatter;

final class Coder implements Formatter
{
    use Chaining;

    /**
     * @var CodeMap Code map for coding character trees.
     */
    private $codeMap;

    /**
     * @var CharTree[] Memoization of formatted character trees.
     */
    private $memoization = [];

    /**
     * @param CodeMap $codeMap Code map for coding character trees.
     */
    public function __construct(CodeMap $codeMap)
    {
        $this->codeMap = $codeMap;
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Coded variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        return $this->applyInternal($charTree);
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Coded variant of the character tree.
     */
    private function applyInternal(CharTree $charTree): CharTree
    {
        // When PHP 7.1 is no longer supported, change to using spl_object_id.
        $hash = spl_object_hash($charTree);

        if (!isset($this->memoization[$hash])) {
            $this->memoization[$hash] = $this->format($charTree);
        }

        return $this->memoization[$hash];
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Coded variant of the character tree. Memoization is not used.
     */
    private function format(CharTree $charTree): CharTree
    {
        if ($charTree->getRoot() === null) {
            return $charTree;
        }

        $branches = [];

        foreach ($this->codeMap->getMap($charTree) as $char => $codedChars) {
            $branch = $this->applyInternal($charTree->getBranchesAfterRoot((string)$char));

            foreach ($codedChars as $codedChar) {
                $branches[] = CharTree::fromString($codedChar, [$branch]);
            }
        }

        return CharTree::fromString('', $branches);
    }
}
