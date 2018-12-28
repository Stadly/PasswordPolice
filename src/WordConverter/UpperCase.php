<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use Stadly\PasswordPolice\WordConverter;
use Traversable;

final class UpperCase implements WordConverter
{
    /**
     * {@inheritDoc}
     */
    public function convert(string $word): Traversable
    {
        yield mb_strtoupper($word);
    }
}
