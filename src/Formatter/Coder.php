<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use SplObjectStorage;
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
     * @var SplObjectStorage Memoization for already coded character trees.
     */
    private $codeMemoization;

    /**
     * @param CodeMap $codeMap Code map for coding character trees.
     */
    public function __construct(CodeMap $codeMap)
    {
        $this->codeMap = $codeMap;
        $this->codeMemoization = new SplObjectStorage();
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
        if (!isset($this->codeMemoization[$charTree])) {
            $this->codeMemoization[$charTree] = $this->code($charTree);
        }

        return $this->codeMemoization[$charTree];
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Coded variant of the character tree. Memoization is not used.
     */
    private function code(CharTree $charTree): CharTree
    {
        $charTrees = [];

        foreach ($this->codeMap->getMap($charTree) as $char => $codedChars) {
            $branch = $this->applyInternal($charTree->getBranchesAfterRoot((string)$char));

            foreach ($codedChars as $codedChar) {
                $charTrees[] = CharTree::fromString($codedChar, [$branch]);
            }
        }

        return CharTree::fromArray($charTrees);
    }
}
