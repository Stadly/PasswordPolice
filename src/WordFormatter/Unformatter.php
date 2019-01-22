<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Traversable;

final class Unformatter extends ChainableFormatter
{
    /**
     * @param iterable<string> $words Words.
     * @return Traversable<string> The same words.
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        yield from $words;
    }
}
