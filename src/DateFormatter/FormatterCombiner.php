<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTimeInterface;
use Stadly\PasswordPolice\DateFormatter;
use Stadly\PasswordPolice\WordFormatter\UniqueFilter;
use Traversable;

final class FormatterCombiner implements DateFormatter
{
    use FormatterChaining;

    /**
     * @var DateFormatter[] Date formatters.
     */
    private $dateFormatters;

    /**
     * @param DateFormatter[] $dateFormatters Date formatters.
     * @param bool $filterUnique Whether the result should only contain unique words.
     */
    public function __construct(array $dateFormatters, bool $filterUnique = true)
    {
        if ($filterUnique) {
            $formatterCombiner = new FormatterCombiner($dateFormatters, false);
            $formatterCombiner->setNext(new UniqueFilter());
            $dateFormatters = [$formatterCombiner];
        }

        $this->dateFormatters = $dateFormatters;
    }

    /**
     * @param iterable<DateTimeInterface> $dates Dates to format.
     * @return Traversable<string> The date formatted by all the date formatters in the formatter combiner.
     */
    protected function applyCurrent(iterable $dates): Traversable
    {
        foreach ($this->dateFormatters as $dateFormatter) {
            yield from $dateFormatter->apply($dates);
        }
    }
}
