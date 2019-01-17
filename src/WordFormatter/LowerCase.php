<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class LowerCase implements WordFormatter
{
    /**
     * {@inheritDoc}
     */
    public function apply(string $word): Traversable
    {
        yield mb_strtolower($word);
    }
}
