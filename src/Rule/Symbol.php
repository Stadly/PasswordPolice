<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Stadly\PasswordPolice\Constraint\Count;
use Stadly\PasswordPolice\Policy;

final class Symbol extends CharacterClass
{
    /**
     * {@inheritDoc}
     */
    protected function getMessage(Count $constraint, int $count): string
    {
        $translator = Policy::getTranslator();

        if ($constraint->getMax() === null) {
            return $translator->trans(
                'There must be at least one symbol (%characters%).|'.
                'There must be at least %count% symbols (%characters%).',
                [
                    '%count%' => $constraint->getMin(),
                    '%characters%' => $this->characters,
                ]
            );
        }

        if ($constraint->getMax() === 0) {
            return $translator->trans(
                'There must be no symbols (%characters%).',
                ['%characters%' => $this->characters]
            );
        }

        if ($constraint->getMin() === 0) {
            return $translator->trans(
                'There must be at most one symbol (%characters%).|'.
                'There must be at most %count% symbols (%characters%).',
                [
                    '%count%' => $constraint->getMax(),
                    '%characters%' => $this->characters,
                ]
            );
        }

        if ($constraint->getMin() === $constraint->getMax()) {
            return $translator->trans(
                'There must be exactly one symbol (%characters%).|'.
                'There must be exactly %count% symbols (%characters%).',
                [
                    '%count%' => $constraint->getMin(),
                    '%characters%' => $this->characters,
                ]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% symbols (%characters%).',
            [
                '%min%' => $constraint->getMin(),
                '%max%' => $constraint->getMax(),
                '%characters%' => $this->characters,
            ]
        );
    }
}