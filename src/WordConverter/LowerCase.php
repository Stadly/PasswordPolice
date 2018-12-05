<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use Traversable;

final class LowerCase implements WordConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function convert(string $word): Traversable
    {
        yield mb_strtolower($word);
    }
}
