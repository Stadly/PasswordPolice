<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\DateFormatter;

use DateTimeInterface;
use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\DateFormatter;

final class DefaultFormatter implements DateFormatter
{
    use Chaining;

    private const FORMATS = [
        // Year month day
        [['Y', 'y'], ['m'], ['d']], // 2018-08-04
        [['Y', 'y'], ['n'], ['j']], // 2018-8-4

        // Month day year
        [['m'], ['d'], ['Y', 'y']], // 08-04-2018
        [['n'], ['j'], ['Y', 'y']], // 8-4-2018

        // Day month year
        [['d'], ['m'], ['Y', 'y']], // 04-08-2018
        [['j'], ['n'], ['Y', 'y']], // 4-8-2018
    ];

    private const SEPARATORS = [
        '',
        ' ',
        '-',
        '/',
        '.',
    ];

    /**
     * @var array<string>
     */
    private $formats = [];

    public function __construct()
    {
        foreach (self::FORMATS as [$parts1, $parts2, $parts3]) {
            foreach ($parts1 as $part1) {
                foreach ($parts2 as $part2) {
                    foreach ($parts3 as $part3) {
                        foreach (self::SEPARATORS as $separator) {
                            $this->formats[] = $part1 . $separator . $part2 . $separator . $part3;
                        }
                    }
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
