<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class UpperCase implements WordFormatter
{
    /**
     * {@inheritDoc}
     */
    public function apply(iterable $words): Traversable
    {
        foreach ($words as $word) {
            yield mb_strtoupper($word);
        }
    }
}