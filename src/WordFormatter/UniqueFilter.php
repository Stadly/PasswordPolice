<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Traversable;

final class UniqueFilter extends ChainableFormatter
{
    /**
     * @param iterable<string> $words Words to filter.
     * @return Traversable<string> Unique words.
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        $unique = [];
        foreach ($words as $word) {
            if (isset($unique[$word])) {
                continue;
            }

            $unique[$word] = true;
            yield $word;
        }
    }
}
