<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class LowerCase implements WordFormatter
{
    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> The words with all characters in lower case.
     */
    public function apply(iterable $words): Traversable
    {
        foreach ($words as $word) {
            yield mb_strtolower($word);
        }
    }
}
