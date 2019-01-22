<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use DateTimeInterface;
use Traversable;

/**
 * Interface that must be implemented by all date formatters.
 */
interface DateFormatter
{
    /**
     * @param iterable<DateTimeInterface> $dates Dates to format.
     * @return Traversable<string> Formatted dates.
     */
    public function apply(iterable $dates): Traversable;

    /**
     * @param WordFormatter|null $next Word formatter to apply after this date formatter.
     */
    public function setNext(?WordFormatter $next): void;

    /**
     * @return WordFormatter|null Next formatter in the chain.
     */
    public function getNext(): ?WordFormatter;
}
