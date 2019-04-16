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

    /**
     * @var CharTree[] Memoization of formatted character trees.
     */
    private static $memoization = [];

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
        // When PHP 7.1 is no longer supported, change to using spl_object_id.
        $hash = spl_object_hash($charTree);

        if (!isset($this->memoization[$hash])) {
            $this->memoization[$hash] = $this->mixedCaseCoder->apply($charTree);
        }

        return $this->memoization[$hash];
    }
}
