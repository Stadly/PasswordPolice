<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Stadly\PasswordPolice\Constraint\CountConstraint;
use Stadly\PasswordPolice\Policy;

final class Digit extends CharacterClassRule
{
    /**
     * @param int $min Minimum number of digits.
     * @param int|null $max Maximum number of digits.
     * @param int $weight Constraint weight.
     */
    public function __construct(int $min = 1, ?int $max = null, int $weight = 1)
    {
        parent::__construct('0123456789', $min, $max, $weight);
    }

    /**
     * {@inheritDoc}
     */
    protected function getMessage(CountConstraint $constraint, int $count): string
    {
        $translator = Policy::getTranslator();

        if ($constraint->getMax() === null) {
            return $translator->trans(
                'There must be at least one digit.|'.
                'There must be at least %count% digits.',
                ['%count%' => $constraint->getMin()]
            );
        }

        if ($constraint->getMax() === 0) {
            return $translator->trans(
                'There must be no digits.'
            );
        }

        if ($constraint->getMin() === 0) {
            return $translator->trans(
                'There must be at most one digit.|'.
                'There must be at most %count% digits.',
                ['%count%' => $constraint->getMax()]
            );
        }

        if ($constraint->getMin() === $constraint->getMax()) {
            return $translator->trans(
                'There must be exactly one digit.|'.
                'There must be exactly %count% digits.',
                ['%count%' => $constraint->getMin()]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% digits.',
            ['%min%' => $constraint->getMin(), '%max%' => $constraint->getMax()]
        );
    }
}
