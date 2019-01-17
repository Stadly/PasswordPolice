<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class MixedCase implements WordFormatter
{
    /**
     * {@inheritDoc}
     */
    public function apply(string $word): Traversable
    {
        if ($word === '') {
            yield '';
            return;
        }

        $char = mb_substr($word, 0, 1);

        $chars = [$char];
        if ($char !== mb_strtolower($char)) {
            $chars[] = mb_strtolower($char);
        }
        if ($char !== mb_strtoupper($char)) {
            $chars[] = mb_strtoupper($char);
        }

        foreach ($this->apply(mb_substr($word, 1)) as $suffix) {
            foreach ($chars as $formattedChar) {
                yield $formattedChar.$suffix;
            }
        }
    }
}
