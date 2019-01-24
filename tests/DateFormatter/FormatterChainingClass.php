<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use Traversable;

final class FormatterChainingClass
{
    use FormatterChaining;

    /**
     * {@inheritDoc}
     */
    protected function applyCurrent(iterable $dates): Traversable
    {
        foreach ($dates as $date) {
            yield $date->format('d/m/Y');
        }
    }
}
