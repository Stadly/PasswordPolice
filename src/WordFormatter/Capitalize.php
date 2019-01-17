<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class Capitalize implements WordFormatter
{
    /**
     * {@inheritDoc}
     */
    public function apply(string $word): Traversable
    {
        yield mb_strtoupper(mb_substr($word, 0, 1)).mb_strtolower(mb_substr($word, 1));
    }
}
