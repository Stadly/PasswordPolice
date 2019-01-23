<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTimeInterface;
use Stadly\PasswordPolice\DateFormatter;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

trait FormatterChaining
{
    /**
     * @var WordFormatter|null Next formatter in the chain.
     */
    private $next;

    /**
     * {@inheritDoc}
     */
    public function setNext(?WordFormatter $next): void
    {
        $this->next = $next;
    }

    /**
     * {@inheritDoc}
     */
    public function getNext(): ?WordFormatter
    {
        return $this->next;
    }

    /**
     * @param iterable<DateTimeInterface> $dates Dates to format.
     * @return Traversable<string> The dates formatted by the formatter chain. May contain duplicates.
     */
    public function apply(iterable $dates): Traversable
    {
        if ($this->next === null) {
            yield from $this->applyCurrent($dates);
        } else {
            yield from $this->next->apply($this->applyCurrent($dates));
        }
    }

    /**
     * @param iterable<DateTimeInterface> $dates Dates to format.
     * @return Traversable<string> The dates formatted by this date formatter. May contain duplicates.
     */
    abstract protected function applyCurrent(iterable $dates): Traversable;
}
