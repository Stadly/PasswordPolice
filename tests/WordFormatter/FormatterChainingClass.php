<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Traversable;

final class FormatterChainingClass
{
    use FormatterChaining;

    /**
     * {@inheritDoc}
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        foreach ($words as $word) {
            yield strrev($word);
        }
    }
}
