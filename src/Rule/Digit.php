<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Symfony\Component\Translation\Translator;

final class Digit extends CharacterClass
{
    public function __construct(int $min, ?int $max = null)
    {
        parent::__construct('0123456789', $min, $max);
    }

    public function getMessage(Translator $translator): string
    {
        if ($this->getMax() === null) {
            return $translator->transChoice(
                'There must be at least one digit.|'.
                'There must be at least %count% digits.',
                $this->getMin()
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'There must be no digits.'
            );
        }

        if ($this->getMin() === 0) {
            return $translator->transChoice(
                'There must be at most one digit.|'.
                'There must be at most %count% digits.',
                $this->getMax()
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->transChoice(
                'There must be exactly one digit.|'.
                'There must be exactly %count% digits.',
                $this->getMin()
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% digits.',
            ['%min%' => $this->getMin(), '%max%' => $this->getMax()]
        );
    }
}
