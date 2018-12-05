<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use Traversable;

final class MixedCase implements WordConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function convert(string $word): Traversable
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

        foreach ($this->convert(mb_substr($word, 1)) as $suffix) {
            foreach ($chars as $char) {
                yield $char.$suffix;
            }
        }
    }
}
