<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use Stadly\PasswordPolice\WordConverter;
use Traversable;

final class Capitalize implements WordConverter
{
    /**
     * {@inheritDoc}
     */
    public function convert(string $word): Traversable
    {
        yield mb_strtoupper(mb_substr($word, 0, 1)).mb_strtolower(mb_substr($word, 1));
    }
}
