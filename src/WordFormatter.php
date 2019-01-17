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
}
