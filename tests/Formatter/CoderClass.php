<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Formatter;

use Stadly\PasswordPolice\CharTree;

final class CoderClass extends Coder
{
    use Chaining;

    /**
     * {@inheritDoc}
     */
    protected function applyCurrent(CharTree $charTree): CharTree
    {
        return $this->format($charTree);
    }
}
