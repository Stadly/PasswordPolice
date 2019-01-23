<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\WordFormatter;

use Stadly\PasswordPolice\WordFormatter;
use Traversable;

final class FormatterCombiner implements WordFormatter
{
    use FormatterChaining;

    /**
     * @var WordFormatter[] Word formatters.
     */
    private $wordFormatters;

    /**
     * @param WordFormatter[] $wordFormatters Word formatters.
     * @param bool $includeUnformatted Whether the result should also include the words unformatted.
     * @param bool $filterUnique Whether the result should only contain unique words.
     */
    public function __construct(array $wordFormatters, bool $includeUnformatted = true, bool $filterUnique = true)
    {
        if ($filterUnique) {
            $formatterCombiner = new FormatterCombiner($wordFormatters, $includeUnformatted, false);
            $formatterCombiner->setNext(new UniqueFilter());
            $wordFormatters = [$formatterCombiner];
        } elseif ($includeUnformatted) {
            $wordFormatters[] = new Unformatter();
        }

        $this->wordFormatters = $wordFormatters;
    }

    /**
     * @param iterable<string> $words Words to format.
     * @return Traversable<string> The words formatted by all the word formatters in the formatter combiner.
     */
    protected function applyCurrent(iterable $words): Traversable
    {
        foreach ($this->wordFormatters as $wordFormatter) {
            yield from $wordFormatter->apply($words);
        }
    }
}
