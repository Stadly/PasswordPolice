<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CharTree\Cutter;
use Stadly\PasswordPolice\Formatter;

final class Capitalizer implements Formatter
{
    use Chaining;

    /**
     * @var LowerCaseConverter Lower case converter.
     */
    private $lowerCaseConverter;

    /**
     * @var Cutter Character tree cutter for extracting the first character.
     */
    private $charExtractor;

    public function __construct()
    {
        $this->lowerCaseConverter = new LowerCaseConverter();
        $this->charExtractor = new Cutter();
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Capitalized variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        if ($charTree->getRoot() === null) {
            return $charTree;
        }

        $formatted = [];

        foreach ($this->charExtractor->cut($charTree, 1) as [$root, $tree]) {
            assert(is_string($root));
            assert(is_object($tree));

            $branches = [$this->lowerCaseConverter->apply($tree)];
            $formatted[] = CharTree::fromString(mb_strtoupper($root), $branches);
        }

        return CharTree::fromString('', $formatted);
    }
}
