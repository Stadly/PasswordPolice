<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Stadly\PasswordPolice\Policy;

final class Symbol extends CharacterClass
{
    /**
     * {@inheritDoc}
     */
    protected function getMessage(): string
    {
        $translator = Policy::getTranslator();

        if ($this->max === null) {
            return $translator->trans(
                'There must be at least one symbol (%characters%).|'.
                'There must be at least %count% symbols (%characters%).',
                [
                    '%count%' => $this->min,
                    '%characters%' => $this->characters,
                ]
            );
        }

        if ($this->max === 0) {
            return $translator->trans(
                'There must be no symbols (%characters%).',
                ['%characters%' => $this->characters]
            );
        }

        if ($this->min === 0) {
            return $translator->trans(
                'There must be at most one symbol (%characters%).|'.
                'There must be at most %count% symbols (%characters%).',
                [
                    '%count%' => $this->max,
                    '%characters%' => $this->characters,
                ]
            );
        }

        if ($this->min === $this->max) {
            return $translator->trans(
                'There must be exactly one symbol (%characters%).|'.
                'There must be exactly %count% symbols (%characters%).',
                [
                    '%count%' => $this->min,
                    '%characters%' => $this->characters,
                ]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% symbols (%characters%).',
            [
                '%min%' => $this->min,
                '%max%' => $this->max,
                '%characters%' => $this->characters,
            ]
        );
    }
}
