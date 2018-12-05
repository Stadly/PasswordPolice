<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use Traversable;

final class UpperCase implements WordConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function convert(string $word): Traversable
    {
        yield mb_strtoupper($word);
    }
}
