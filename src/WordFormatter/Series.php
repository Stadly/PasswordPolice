<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use InvalidArgumentException;
use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class Series implements WordFormatter
{
    /**
     * @var WordFormatter[] Word formatters.
     */
    private $wordFormatters;

    /**
     * @param WordFormatter ...$wordFormatters Word formatters.
     */
    public function __construct(WordFormatter ...$wordFormatters)
    {
        if ($wordFormatters === []) {
            throw new InvalidArgumentException('At least one word formatter must be specified.');
        }

        $this->wordFormatters = $wordFormatters;
    }

    /**
     * {@inheritDoc}
     */
    public function apply(string $word): Traversable
    {
        yield from $this->formatWord($word, $this->wordFormatters);
    }

    private function formatWord(string $word, array $wordFormatters): Traversable
    {
        $wordFormatter = array_shift($wordFormatters);

        if ($wordFormatters === []) {
            yield from $wordFormatter->apply($word);
            return;
        }

        foreach ($wordFormatter->apply($word) as $formatted) {
            yield from $this->formatWord($formatted, $wordFormatters);
        }
    }
}
