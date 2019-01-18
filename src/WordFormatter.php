<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use Traversable;

/**
 * Interface that must be implemented by all word formatters.
 */
interface WordFormatter
{
    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> Formatted words. May contain duplicates.
     */
    public function apply(iterable $words): Traversable;

    /**
     * @param WordFormatter|null $next Word formatter to apply after this one.
     */
    public function setNext(?WordFormatter $next): void;

    /**
     * @return WordFormatter|null Next word formatter in the chain.
     */
    public function getNext(): ?WordFormatter;
}
