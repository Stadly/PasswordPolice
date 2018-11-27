<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CaseConverter;

final class UpperCase implements CaseConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function convert(string $word): string
    {
        return mb_strtoupper($word);
    }
}
