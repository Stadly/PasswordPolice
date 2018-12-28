<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\Date;
use Carbon\CarbonInterval;
use Stadly\Date\Interval;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;

final class Change implements Rule
{
    /**
     * @var Date[] Rule constraints.
     */
    private $constraints;

    /**
     * @param DateInterval $min Minimum time since last password change.
     * @param DateInterval|null $max Maximum time since last password change.
     * @param int $weight Constraint weight.
     */
    public function __construct(DateInterval $min, ?DateInterval $max = null, int $weight = 1)
    {
        $this->addConstraint($min, $max, $weight);
    }

    /**
     * @param DateInterval $min Minimum time since last password change.
     * @param DateInterval|null $max Maximum time since last password change.
     * @param int $weight Constraint weight.
     * @return $this
     */
    public function addConstraint(DateInterval $min, ?DateInterval $max = null, int $weight = 1): self
    {
        $this->constraints[] = new Date($min, $max, $weight);

        StableSort::usort($this->constraints, function (Date $a, Date $b): int {
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
        $locale = $translator->getLocale();

        $min = CarbonInterval::instance($constraint->getMin());
        $min->locale($locale);
        if ($constraint->getMax() === null) {
            $max = null;
        } else {
            $max = CarbonInterval::instance($constraint->getMax());
            $max->locale($locale);
        }

        if ($constraint->getMax() === null) {
            return $translator->trans(
                'Must be at least %interval% between password changes.',
                ['%interval%' => $min]
            );
        }

        if (0 === Interval::compare(new DateInterval('PT0S'), $min)) {
            return $translator->trans(
                'Must be at most %interval% between password changes.',
                ['%interval%' => $max]
            );
        }

        if (Interval::compare($constraint->getMin(), $constraint->getMax()) === 0) {
            return $translator->trans(
                'Must be exactly %interval% between password changes.',
                ['%interval%' => $min]
            );
        }

        return $translator->trans(
            'Must be between %min% and %max% between password changes.',
            ['%min%' => $min, '%max%' => $max]
        );
    }
}
