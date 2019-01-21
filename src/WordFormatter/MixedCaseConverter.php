<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Traversable;

final class MixedCaseConverter extends ChainableFormatter
{
    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> Variants of the words with all combinations of upper case and lower case characters.
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        foreach ($words as $word) {
            yield from $this->formatWord($word);
        }
    }

    /**
     * @param string $word Word to format.
     * @return Traversable<string> Variants of the word with all combinations of upper case and lower case characters.
     */
    private function formatWord(string $word): Traversable
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

        foreach ($this->formatWord(mb_substr($word, 1)) as $suffix) {
            foreach ($chars as $formattedChar) {
                yield $formattedChar.$suffix;
            }
        }
    }
}
