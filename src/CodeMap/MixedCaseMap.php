<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;
use Stadly\PasswordPolice\Formatter\LengthFilter;
use Stadly\PasswordPolice\Formatter\Truncator;

final class MixedCaseMap implements CodeMap
{
    /**
     * @var Truncator Formatter for extracting the first character.
     */
    private $charExtractor;

    public function __construct()
    {
        $this->charExtractor = new Truncator(1);
        $this->charExtractor->setNext(new LengthFilter(1, 1));
    }

    /**
     * {@inheritDoc}
     */
    public function getMap(CharTree $charTree): array
    {
        $codeMap = [];
        foreach ($this->charExtractor->apply($charTree) as $char) {
            $lowerCase = mb_strtolower($char);
            $upperCase = mb_strtoupper($char);

            $codeMap[$char] = [$lowerCase];

            if ($lowerCase !== $upperCase) {
                $codeMap[$char][] = $upperCase;
            }
        }
        return $codeMap;
    }

    /**
     * {@inheritDoc}
     */
    public function getLengths(): array
    {
        return [1];
    }

    /**
     * {@inheritDoc}
     */
    public function code(string $string): array
    {
        $lowerCase = mb_strtolower($string);
        $upperCase = mb_strtoupper($string);

        $codeMap = [$lowerCase];
        if ($lowerCase !== $upperCase) {
            $codeMap[] = $upperCase;
        }

        return $codeMap;
    }
}
