<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Stadly\PasswordPolice\Constraint\CountConstraint;

final class CharacterClassRuleClass extends CharacterClassRule
{
    /**
     * {@inheritDoc}
     */
    protected function getMessage(CountConstraint $constraint, int $count): string
    {
        return 'foo';
    }
}
