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
        return $this->leetspeakCoder->apply($charTree);
    }
}
