<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

/**
 * Interface that must be implemented by all character tree formatters.
 */
interface Formatter
{
    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree Formatted character tree.
     */
    public function apply(CharTree $charTree): CharTree;

    /**
     * @param Formatter|null $next Formatter to apply after this one.
     */
    public function setNext(?Formatter $next): void;

    /**
     * @return Formatter|null Next formatter in the chain.
     */
    public function getNext(): ?Formatter;
}
