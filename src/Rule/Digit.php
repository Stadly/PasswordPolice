<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Stadly\PasswordPolice\Policy;

final class Digit extends CharacterClass
{
    /**
     * @param int $min Minimum number of digits.
     * @param int|null $max Maximum number of digits.
     */
    public function __construct(int $min = 1, ?int $max = null)
    {
        parent::__construct('0123456789', $min, $max);
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        $translator = Policy::getTranslator();

        if ($this->max === null) {
            return $translator->trans(
                'There must be at least one digit.|'.
                'There must be at least %count% digits.',
                ['%count%' => $this->min]
            );
        }

        if ($this->max === 0) {
            return $translator->trans(
                'There must be no digits.'
            );
        }

        if ($this->min === 0) {
            return $translator->trans(
                'There must be at most one digit.|'.
                'There must be at most %count% digits.',
                ['%count%' => $this->max]
            );
        }

        if ($this->min === $this->max) {
            return $translator->trans(
                'There must be exactly one digit.|'.
                'There must be exactly %count% digits.',
                ['%count%' => $this->min]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% digits.',
            ['%min%' => $this->min, '%max%' => $this->max]
        );
    }
}
