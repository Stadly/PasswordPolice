<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CaseConverter;

final class Capitalize implements CaseConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function convert(string $word): string
    {
        return mb_strtoupper(mb_substr($word, 0, 1)).mb_strtolower(mb_substr($word, 1));
    }
}
