<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;
use Stadly\PasswordPolice\Formatter;

class Coder implements Formatter
{
    use Chaining;

    /**
     * @var CodeMap Code map for coding character trees.
     */
    private $codeMap;

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
        return $this->format($charTree);
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
            $branch = $this->applyCurrent($charTree->getBranchesAfterRoot((string)$char));

            foreach ($codedChars as $codedChar) {
                $branches[] = CharTree::fromString($codedChar, [$branch]);
            }
        }

        return CharTree::fromString('', $branches);
    }
}
