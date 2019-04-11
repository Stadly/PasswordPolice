<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap\LowerCaseMap;
use Stadly\PasswordPolice\Formatter;

final class Capitalizer implements Formatter
{
    use Chaining;

    /**
     * @var Coder Lower case coder.
     */
    private $lowerCaseCoder;

    public function __construct()
    {
        $this->lowerCaseCoder = new Coder(new LowerCaseMap());
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Capitalized variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        $formatted = [];

        foreach ($charTree->getTreeTrimmedToLength(1) as $char) {
            $branches = [$this->lowerCaseCoder->apply($charTree->getBranchesAfterRoot($char))];
            $formatted[] = CharTree::fromString(mb_strtoupper($char), $branches);
        }

        return CharTree::fromArray($formatted);
    }
}
