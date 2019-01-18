<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class Series extends ChainableFormatter
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
        $this->wordFormatters = $wordFormatters;
    }

    /**
     * {@inheritDoc}
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        yield from $this->formatWord($words, $this->wordFormatters);
    }

    /**
     * @param iterable<string> $words Words to format.
     * @param WordFormatter[] $wordFormatters Word formatters.
     * @return Traversable<string> Formatted words. May contain duplicates.
     */
    private function formatWord(iterable $words, array $wordFormatters): Traversable
    {
        if ($wordFormatters === []) {
            yield from $words;
            return;
        }

        $wordFormatter = array_shift($wordFormatters);
        yield from $this->formatWord($wordFormatter->apply($words), $wordFormatters);
    }
}
