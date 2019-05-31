<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTimeInterface;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

trait Chaining
{
    /**
     * @var Formatter|null Next character tree formatter in the chain.
     */
    private $next = null;

    /**
     * @param Formatter|null $next Formatter to apply after this date formatter.
     */
    public function setNext(?Formatter $next): void
    {
        $this->next = $next;
    }

    /**
     * @return Formatter|null Next formatter in the chain.
     */
    public function getNext(): ?Formatter
    {
        return $this->next;
    }

    /**
     * @param iterable<DateTimeInterface> $dates Dates to format.
     * @return CharTree The dates formatted by the formatter chain.
     */
    public function apply(iterable $dates): CharTree
    {
        if ($this->next === null) {
            return $this->applyCurrent($dates);
        } else {
            return $this->next->apply($this->applyCurrent($dates));
        }
    }

    /**
     * @param iterable<DateTimeInterface> $dates Dates to format.
     * @return CharTree The dates formatted by this date formatter.
     */
    abstract protected function applyCurrent(iterable $dates): CharTree;
}
