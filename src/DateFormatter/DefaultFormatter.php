<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTimeInterface;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\DateFormatter;

final class DefaultFormatter implements DateFormatter
{
    use Chaining;

    private const DATE_FORMATS = [
        // Year
        ['Y'], // 2018

        // Year month
        ['y', 'n'], // 18 8
        ['y', 'm'], // 18 08
        ['y', 'M'], // 18 Aug
        ['y', 'F'], // 18 August

        // Month year
        ['n', 'y'], // 8 18
        ['M', 'y'], // Aug 18
        ['F', 'y'], // August 18

        // Day month
        ['j', 'n'], // 4 8
        ['j', 'm'], // 4 08
        ['j', 'M'], // 4 Aug
        ['j', 'F'], // 4 August

        // Month day
        ['n', 'j'], // 8 4
        ['n', 'd'], // 8 04
        ['M', 'j'], // Aug 4
        ['M', 'd'], // Aug 04
        ['F', 'j'], // August 4
        ['F', 'd'], // August 04
    ];

    private const DATE_SEPARATORS = [
        '',
        '-',
        ' ',
        '/',
        '.',
        ',',
        '. ',
        ', ',
    ];

    /**
     * @var array<string>
     */
    private $formats = [];

    public function __construct()
    {
        foreach (self::DATE_FORMATS as $format) {
            if (count($format) === 1) {
                $this->formats[] = reset($format);
            } else {
                foreach (self::DATE_SEPARATORS as $separator) {
                    $this->formats[] = implode($separator, $format);
                }
            }
        }
    }

    /**
     * @param iterable<DateTimeInterface> $dates Dates to format.
     * @return CharTree The formatted dates.
     */
    protected function applyCurrent(iterable $dates): CharTree
    {
        $charTrees = [];

        foreach ($dates as $date) {
            foreach ($this->formats as $format) {
                $charTrees[] = CharTree::fromString($date->format($format));
            }
        }

        return CharTree::fromArray($charTrees);
    }
}
