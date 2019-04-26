<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;
use Stadly\PasswordPolice\Formatter;

trait Chaining
{
    /**
     * @var Formatter|null Next character tree formatter in the chain.
     */
    private $next = null;

    /**
     * {@inheritDoc}
     */
    public function setNext(?Formatter $next): void
    {
        $this->next = $next;
    }

    /**
     * {@inheritDoc}
     */
    public function getNext(): ?Formatter
    {
        return $this->next;
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree The character tree formatted by the formatter chain.
     */
    public function apply(CharTree $charTree): CharTree
    {
        if ($this->next === null) {
            return $this->applyCurrent($charTree);
        } else {
            return $this->next->apply($this->applyCurrent($charTree));
        }
    }

    /**
     * @param CharTree $charTree Character tree to format.
     * @return CharTree The character tree formatted by this formatter.
     */
    abstract protected function applyCurrent(CharTree $charTree): CharTree;
}
