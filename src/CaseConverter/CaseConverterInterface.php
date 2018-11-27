<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CaseConverter;

/**
 * Interface that must be implemented by all case converters.
 */
interface CaseConverterInterface
{
    /**
     * @param string $word Word to convert.
     * @return string Case-converted word.
     */
    public function convert(string $word): string;
}
