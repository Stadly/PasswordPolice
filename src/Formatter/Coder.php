<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CharTree\Cutter;
use Stadly\PasswordPolice\CodeMap;
use Stadly\PasswordPolice\Formatter;

abstract class Coder implements Formatter
{
    /**
     * @var CodeMap Code map for coding character trees.
     */
    private $codeMap;

    /**
     * @var Cutter Character tree cutter for extracting the first character.
     */
    private $charExtractor;

    /**
     * @param CodeMap $codeMap Code map for coding character trees.
     */
    public function __construct(CodeMap $codeMap)
    {
        $this->codeMap = $codeMap;
        $this->charExtractor = new Cutter();
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

        $formatted = [];

        foreach ($this->codeMap->getLengths() as $length) {
            foreach ($this->charExtractor->cut($charTree, $length) as [$root, $tree]) {
                assert(is_string($root));
                assert(is_object($tree));

                $branch = $this->applyCurrent($tree);
                foreach ($this->codeMap->code($root) as $codedChar) {
                    $formatted[] = CharTree::fromString($codedChar, [$branch]);
                }
            }
        }

        return CharTree::fromString('', $formatted);
    }
}
