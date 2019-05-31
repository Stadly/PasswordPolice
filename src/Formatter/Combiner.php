<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

final class Combiner implements Formatter
{
    use Chaining;

    /**
     * @var array<Formatter> Character tree formatters.
     */
    private $formatters;

    /**
     * @var bool Whether the result should also include the character tree unformatted.
     */
    private $includeUnformatted;

    /**
     * @param array<Formatter> $formatters Character tree formatters.
     * @param bool $includeUnformatted Whether the result should also include the character tree unformatted.
     */
    public function __construct(array $formatters, bool $includeUnformatted = true)
    {
        $this->formatters = $formatters;
        $this->includeUnformatted = $includeUnformatted;
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree The character tree formatted by all the formatters in the combiner.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        $charTrees = [];

        if ($this->includeUnformatted) {
            $charTrees[] = $charTree;
        }

        foreach ($this->formatters as $formatter) {
            $charTrees[] = $formatter->apply($charTree);
        }

        return CharTree::fromArray($charTrees);
    }
}
