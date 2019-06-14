<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Stadly\PasswordPolice\Constraint\CountConstraint;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SymbolRule extends CharacterClassRule
{
    protected function getMessage(CountConstraint $constraint, int $count, TranslatorInterface $translator): string
    {
        if ($constraint->getMax() === null) {
            return $translator->trans(
                'The password must contain at least one symbol (%characters%).|' .
                'The password must contain at least %count% symbols (%characters%).',
                [
                    '%count%' => $constraint->getMin(),
                    '%characters%' => $this->characters,
                ]
            );
        }

        if ($constraint->getMax() === 0) {
            return $translator->trans(
                'The password cannot contain symbols (%characters%).',
                ['%characters%' => $this->characters]
            );
        }

        if ($constraint->getMin() === 0) {
            return $translator->trans(
                'The password must contain at most one symbol (%characters%).|' .
                'The password must contain at most %count% symbols (%characters%).',
                [
                    '%count%' => $constraint->getMax(),
                    '%characters%' => $this->characters,
                ]
            );
        }

        if ($constraint->getMin() === $constraint->getMax()) {
            return $translator->trans(
                'The password must contain exactly one symbol (%characters%).|' .
                'The password must contain exactly %count% symbols (%characters%).',
                [
                    '%count%' => $constraint->getMin(),
                    '%characters%' => $this->characters,
                ]
            );
        }

        return $translator->trans(
            'The password must contain between %min% and %max% symbols (%characters%).',
            [
                '%min%' => $constraint->getMin(),
                '%max%' => $constraint->getMax(),
                '%characters%' => $this->characters,
            ]
        );
    }
}
