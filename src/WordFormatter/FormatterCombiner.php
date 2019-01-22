<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class FormatterCombiner extends ChainableFormatter
{
    /**
     * @var WordFormatter[] Word formatters.
     */
    private $wordFormatters;

    /**
     * @var bool Whether the result should only contain unique words.
     */
    private $filterUnique;

    /**
     * @param WordFormatter[] $wordFormatters Word formatters.
     * @param bool $includeUnformatted Whether the result should also include the words unformatted.
     * @param bool $filterUnique Whether the result should only contain unique words.
     */
    public function __construct(array $wordFormatters, bool $includeUnformatted = true, bool $filterUnique = true)
    {
        if ($includeUnformatted) {
            $wordFormatters[] = new Unformatter();
        }

        $this->wordFormatters = $wordFormatters;
        $this->filterUnique = $filterUnique;
    }

    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> The words formatted by all the word formatters in the formatter combiner.
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        $formatted = $this->applyFormatters($words);

        if ($this->filterUnique) {
            $uniqueFilter = new UniqueFilter();
            yield from $uniqueFilter->apply($formatted);
        } else {
            yield from $formatted;
        }
    }

    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> The words formatted by all the word formatters in the formatter combiner.
     */
    private function applyFormatters(iterable $words): Traversable
    {
        foreach ($this->wordFormatters as $wordFormatter) {
            yield from $wordFormatter->apply($words);
        }
    }
}
