<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;
use Stadly\PasswordPolice\Formatter;

abstract class Coder implements Formatter
{
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
    abstract protected function applyCurrent(CharTree $charTree): CharTree;

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Coded variant of the character tree. Memoization is not used.
     */
    protected function format(CharTree $charTree): CharTree
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
