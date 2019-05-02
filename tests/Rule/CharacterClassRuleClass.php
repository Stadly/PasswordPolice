<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Stadly\PasswordPolice\Constraint\CountConstraint;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CharacterClassRuleClass extends CharacterClassRule
{
    /**
     * {@inheritDoc}
     */
    protected function getMessage(CountConstraint $constraint, int $count, TranslatorInterface $translator): string
    {
        return 'foo';
    }
}
