<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap\LeetspeakMap;
use Stadly\PasswordPolice\Formatter;

final class LeetspeakDecoder implements Formatter
{
    use Chaining;

    /**
     * @var Coder Leetspeak coder.
     */
    private $leetspeakCoder;

    /**
     * @var CharTree[] Memoization of formatted character trees.
     */
    private static $memoization = [];

    public function __construct()
    {
        $this->leetspeakCoder = new Coder(new LeetspeakMap());
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Leetspeak decoded variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        // When PHP 7.1 is no longer supported, change to using spl_object_id.
        $hash = spl_object_hash($charTree);

        if (!isset(self::$memoization[$hash])) {
            self::$memoization[$hash] = $this->leetspeakCoder->apply($charTree);
        }

        return self::$memoization[$hash];
    }
}
