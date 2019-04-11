<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap\MixedCaseMap;
use Stadly\PasswordPolice\Formatter;

final class MixedCaseConverter implements Formatter
{
    use Chaining;

    /**
     * @var Coder Mixed case coder.
     */
    private $mixedCaseCoder;

    public function __construct()
    {
        $this->mixedCaseCoder = new Coder(new MixedCaseMap());
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Mixed case converted variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        return $this->mixedCaseCoder->apply($charTree);
    }
}
