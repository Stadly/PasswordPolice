<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTimeInterface;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\DateFormatter;

final class Combiner implements DateFormatter
{
    use Chaining;

    /**
     * @var array<DateFormatter> Date formatters.
     */
    private $dateFormatters;

    /**
     * @param array<DateFormatter> $dateFormatters Date formatters.
     */
    public function __construct(array $dateFormatters)
    {
        $this->dateFormatters = $dateFormatters;
    }

    /**
     * @param iterable<DateTimeInterface> $dates Dates to format.
     * @return CharTree The dates formatted by all the date formatters in the combiner.
     */
    protected function applyCurrent(iterable $dates): CharTree
    {
        $charTrees = [];

        foreach ($this->dateFormatters as $dateFormatter) {
            $charTrees[] = $dateFormatter->apply($dates);
        }

        return CharTree::fromArray($charTrees);
    }
}
