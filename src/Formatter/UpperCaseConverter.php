<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap\UpperCaseMap;
use Stadly\PasswordPolice\Formatter;

final class UpperCaseConverter implements Formatter
{
    use Chaining;

    /**
     * @var Coder Upper case coder.
     */
    private $upperCaseCoder;

    public function __construct()
    {
        $this->upperCaseCoder = new Coder(new UpperCaseMap());
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Upper case converted variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        return $this->upperCaseCoder->apply($charTree);
    }
}
