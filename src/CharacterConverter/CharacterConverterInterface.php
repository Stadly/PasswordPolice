<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CharacterConverter;

use Traversable;

/**
 * Interface that must be implemented by all character converters.
 */
interface CharacterConverterInterface
{
    /**
     * @param string $word Word to convert.
     * @return Traversable<string> Character-converted words.
     */
    public function convert(string $word): Traversable;
}
