<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\CountConstraint;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class LengthRule implements Rule
{
    /**
     * @var array<CountConstraint> Rule constraints.
     */
    private $constraints = [];

    /**
     * @param int $min Minimum password length.
     * @param int|null $max Maximum password length.
     * @param int $weight Constraint weight.
     */
    public function __construct(int $min = 8, ?int $max = null, int $weight = 1)
    {
        $this->addConstraint($min, $max, $weight);
    }

    /**
     * @param int $min Minimum password length.
     * @param int|null $max Maximum password length.
     * @param int $weight Constraint weight.
     * @return $this
     */
    public function addConstraint(int $min = 8, ?int $max = null, int $weight = 1): self
    {
        $this->constraints[] = new CountConstraint($min, $max, $weight);

        StableSort::usort($this->constraints, static function (CountConstraint $a, CountConstraint $b): int {
            return $b->getWeight() <=> $a->getWeight();
        });

        return $this;
    }

    /**
     * Check whether a password is in compliance with the rule.
     *
     * @param Password|string $password Password to check.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return bool Whether the password is in compliance with the rule.
     */
    public function test($password, ?int $weight = 1): bool
    {
        $count = $this->getCount((string)$password);
        $constraint = $this->getViolation($count, $weight);

        return $constraint === null;
    }

    /**
     * Validate that a password is in compliance with the rule.
     *
     * @param Password|string $password Password to validate.
     * @param TranslatorInterface&LocaleAwareInterface $translator Translator for translating messages.
     * @return ValidationError|null Validation error describing why the password is not in compliance with the rule.
     */
    public function validate($password, TranslatorInterface $translator): ?ValidationError
    {
        $count = $this->getCount((string)$password);
        $constraint = $this->getViolation($count);

        if ($constraint !== null) {
            return new ValidationError(
                $this->getMessage($constraint, $count, $translator),
                $password,
                $this,
                $constraint->getWeight()
            );
        }

        return null;
    }

    /**
     * @param int $count Number of characters.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return CountConstraint|null Constraint violated by the count.
     */
    private function getViolation(int $count, ?int $weight = null): ?CountConstraint
    {
        foreach ($this->constraints as $constraint) {
            if ($weight !== null && $constraint->getWeight() < $weight) {
                continue;
            }
            if (!$constraint->test($count)) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * @param string $password Password to count characters in.
     * @return int Number of characters.
     */
    private function getCount(string $password): int
    {
        return mb_strlen($password);
    }

    /**
     * @param CountConstraint $constraint Constraint that is violated.
     * @param int $count Count that violates the constraint.
     * @param TranslatorInterface&LocaleAwareInterface $translator Translator for translating messages.
     * @return string Message explaining the violation.
     */
    private function getMessage(CountConstraint $constraint, int $count, TranslatorInterface $translator): string
    {
        if ($constraint->getMax() === null) {
            return $translator->trans(
                'The password must contain at least one character.|' .
                'The password must contain at least %count% characters.',
                ['%count%' => $constraint->getMin()]
            );
        }

        if ($constraint->getMax() === 0) {
            return $translator->trans(
                'The password cannot contain characters.'
            );
        }

        if ($constraint->getMin() === 0) {
            return $translator->trans(
                'The password must contain at most one character.|' .
                'The password must contain at most %count% characters.',
                ['%count%' => $constraint->getMax()]
            );
        }

        if ($constraint->getMin() === $constraint->getMax()) {
            return $translator->trans(
                'The password must contain exactly one character.|' .
                'The password must contain exactly %count% characters.',
                ['%count%' => $constraint->getMin()]
            );
        }

        return $translator->trans(
            'The password must contain between %min% and %max% characters.',
            ['%min%' => $constraint->getMin(), '%max%' => $constraint->getMax()]
        );
    }
}
