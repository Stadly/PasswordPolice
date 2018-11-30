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

        if ($this->getMax() === null) {
            return $translator->trans(
                'There must be at least one digit.|'.
                'There must be at least %count% digits.',
                ['%count%' => $this->getMin()]
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'There must be no digits.'
            );
        }

        if ($this->getMin() === 0) {
            return $translator->trans(
                'There must be at most one digit.|'.
                'There must be at most %count% digits.',
                ['%count%' => $this->getMax()]
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->trans(
                'There must be exactly one digit.|'.
                'There must be exactly %count% digits.',
                ['%count%' => $this->getMin()]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% digits.',
            ['%min%' => $this->getMin(), '%max%' => $this->getMax()]
        );
    }
}
