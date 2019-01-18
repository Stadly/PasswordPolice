<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class UniqueFilter implements WordFormatter
{
    /**
     * @param iterable<string> $words Words to filter.
     * @return Traversable<string> Unique words.
     */
    public function apply(iterable $words): Traversable
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
