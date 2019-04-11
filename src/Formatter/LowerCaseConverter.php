<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap\LowerCaseMap;
use Stadly\PasswordPolice\Formatter;

final class LowerCaseConverter implements Formatter
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
     * @return CharTree Lower case converted variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        return $this->lowerCaseCoder->apply($charTree);
    }
}
