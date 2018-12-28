<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTimeInterface;
use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\Date;
use Carbon\CarbonInterval;
use Stadly\Date\Interval;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;

final class ChangeDate implements Rule
{
    /**
     * @var Date[] Rule constraints.
     */
    private $constraints;

    /**
     * @param DateTimeInterface|null $min Minimum time for when the password was last changed.
     * @param DateTimeInterface|null $max Maximum time for when the password was last changed.
     * @param int $weight Constraint weight.
     */
    public function __construct(?DateTimeInterface $min, ?DateTimeInterface $max = null, int $weight = 1)
    {
        $this->addConstraint($min, $max, $weight);
    }

    /**
     * @param DateTimeInterface|null $min Minimum time for when the password was last changed.
     * @param DateTimeInterface|null $max Maximum time for when the password was last changed.
     * @param int $weight Constraint weight.
     * @return $this
     */
    public function addConstraint(?DateTimeInterface $min, ?DateTimeInterface $max = null, int $weight = 1): self
    {
        $this->constraints[] = new Date($min, $max, $weight);

        StableSort::usort($this->constraints, static function (Date $a, Date $b): int {
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
        $date = $this->getDate($password);
        $constraint = $this->getViolation($date, $weight);

        return $constraint === null;
    }

    /**
     * Validate that a password is in compliance with the rule.
     *
     * @param Password|string $password Password to validate.
     * @return ValidationError|null Validation error describing why the password is not in compliance with the rule.
     */
    public function validate($password): ?ValidationError
    {
        $date = $this->getDate($password);
        $constraint = $this->getViolation($date);

        if ($constraint !== null) {
            assert($date !== null);
            return new ValidationError(
                $this->getMessage($constraint, $date),
                $password,
                $this,
                $constraint->getWeight()
            );
        }

        return null;
    }

    /**
     * @param DateTimeInterface|null $date When the password was last changed.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return Date|null Constraint violated by the count.
     */
    private function getViolation(?DateTimeInterface $date, ?int $weight = null): ?Date
    {
        if ($date === null) {
            return null;
        }

        foreach ($this->constraints as $constraint) {
            if ($weight !== null && $constraint->getWeight() < $weight) {
                continue;
            }
            if (!$constraint->test($date)) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * @param Password|string $password Password to check when was last changed.
     * @return DateTimeInterface|null When the password was last changed.
     */
    private function getDate($password): ?DateTimeInterface
    {
        if ($password instanceof Password) {
            $formerPasswords = $password->getFormerPasswords();

            if ($formerPasswords !== []) {
                return reset($formerPasswords)->getDate();
            }
        }
        return null;
    }

    /**
     * @param Date $constraint Constraint that is violated.
     * @param DateTimeInterface $date Date that violates the constraint.
     * @return string Message explaining the violation.
     */
    private function getMessage(Date $constraint, DateTimeInterface $date): string
    {
        $translator = Policy::getTranslator();
        $minString = $constraint->getMin() === null ? '' : $constraint->getMin()->format('Y-m-d H:i:s');
        $maxString = $constraint->getMax() === null ? '' : $constraint->getMax()->format('Y-m-d H:i:s');

        if ($constraint->getMax() === null) {
            return $translator->trans(
                'The password must have been changed on or after %date%.',
                ['%date%' => $minString]
            );
        }

        if ($constraint->getMin() === null) {
            return $translator->trans(
                'The password must have been changed on or before %date%.',
                ['%date%' => $maxString]
            );
        }

        if ($constraint->getMin() == $constraint->getMax()) {
            return $translator->trans(
                'The password must have been changed at %date%.',
                ['%date%' => $minString]
            );
        }

        return $translator->trans(
            'The password must have been changed between %min% and %max%.',
            ['%min%' => $minString, '%max%' => $maxString]
        );
    }
}
