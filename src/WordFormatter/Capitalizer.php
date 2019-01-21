<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Traversable;

final class Capitalizer extends ChainableFormatter
{
    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> The words with the first character in upper case and the rest in lower case.
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        foreach ($words as $word) {
            yield mb_strtoupper(mb_substr($word, 0, 1)).mb_strtolower(mb_substr($word, 1));
        }
    }
}
