<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap\LowerCaseMap;

final class LowerCaseConverter extends Coder
{
    use Chaining;

    /**
     * @var array<CharTree> Memoization of formatted character trees.
     */
    private static $memoization = [];

    public function __construct()
    {
        parent::__construct(new LowerCaseMap());
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Lower case converted variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        // When PHP 7.1 is no longer supported, change to using spl_object_id.
        $hash = spl_object_hash($charTree);

        if (!isset(self::$memoization[$hash])) {
            self::$memoization[$hash] = $this->format($charTree);
        }

        return self::$memoization[$hash];
    }
}
