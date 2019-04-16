<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

final class Capitalizer implements Formatter
{
    use Chaining;

    /**
     * @var LowerCaseConverter Lower case converter.
     */
    private $lowerCaseConverter;

    /**
     * @var Truncator Formatter for extracting the first character.
     */
    private $charExtractor;

    public function __construct()
    {
        $this->lowerCaseConverter = new LowerCaseConverter();
        $this->charExtractor = new Truncator(1);
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Capitalized variant of the character tree.
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        $formatted = [];

        foreach ($this->charExtractor->apply($charTree) as $char) {
            $branches = [$this->lowerCaseConverter->apply($charTree->getBranchesAfterRoot($char))];
            $formatted[] = CharTree::fromString(mb_strtoupper($char), $branches);
        }

        return CharTree::fromArray($formatted);
    }
}
