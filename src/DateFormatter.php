<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeInterface;

/**
 * Interface that must be implemented by all date formatters.
 */
interface DateFormatter
{
    /**
     * @param iterable<DateTimeInterface> $dates Dates to format.
     * @return CharTree Formatted dates.
     */
    public function apply(iterable $dates): CharTree;

    /**
     * @param Formatter|null $next Formatter to apply after this date formatter.
     */
    public function setNext(?Formatter $next): void;

    /**
     * @return Formatter|null Next formatter in the chain.
     */
    public function getNext(): ?Formatter;
}
