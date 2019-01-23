<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

trait FormatterChaining
{
    /**
     * @var WordFormatter|null Next word formatter in the chain.
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
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> The words formatted by the word formatter chain. May contain duplicates.
     */
    public function apply(iterable $words): Traversable
    {
        if ($this->next === null) {
            yield from $this->applyCurrent($words);
        } else {
            yield from $this->next->apply($this->applyCurrent($words));
        }
    }

    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> The words formatted by this word formatter. May contain duplicates.
     */
    abstract protected function applyCurrent(iterable $words): Traversable;
}
