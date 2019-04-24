<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\CodeMap;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\CodeMap;
use Stadly\PasswordPolice\Formatter\LengthFilter;
use Stadly\PasswordPolice\Formatter\Truncator;

final class LowerCaseMap implements CodeMap
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
            $codeMap[$char] = [mb_strtolower($char)];
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
}
