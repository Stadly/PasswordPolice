<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordConverter;

use Traversable;

/**
 * Interface that must be implemented by all word converters.
 */
interface WordConverterInterface
{
    /**
     * @param string $word Word to convert.
     * @return Traversable<string> Converted words. May contain duplicates.
     */
    public function convert(string $word): Traversable;
}
