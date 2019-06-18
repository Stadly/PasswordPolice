<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use Carbon\CarbonInterval;
use DateInterval;
use DateTimeInterface;
use StableSort\StableSort;
use Stadly\Date\Interval;
use Stadly\PasswordPolice\Constraint\DateIntervalConstraint;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ChangeWithIntervalRule implements Rule
{
    /**
     * @var array<DateIntervalConstraint> Rule constraints.
     */
    private $constraints = [];

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
        $this->constraints[] = new DateIntervalConstraint($min, $max, $weight);

        StableSort::usort($this->constraints, static function (
            DateIntervalConstraint $a,
            DateIntervalConstraint $b
        ): int {
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
     * @param TranslatorInterface&LocaleAwareInterface $translator Translator for translating messages.
     * @return ValidationError|null Validation error describing why the password is not in compliance with the rule.
     */
    public function validate($password, TranslatorInterface $translator): ?ValidationError
    {
        $date = $this->getDate($password);
        $constraint = $this->getViolation($date);

        if ($constraint !== null) {
            assert($date !== null);
            return new ValidationError(
                $this->getMessage($constraint, $date, $translator),
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
     * @return DateIntervalConstraint|null Constraint violated by the date.
     */
    private function getViolation(?DateTimeInterface $date, ?int $weight = null): ?DateIntervalConstraint
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
     * @param DateIntervalConstraint $constraint Constraint that is violated.
     * @param DateTimeInterface $date Date that violates the constraint.
     * @param TranslatorInterface&LocaleAwareInterface $translator Translator for translating messages.
     * @return string Message explaining the violation.
     */
    private function getMessage(
        DateIntervalConstraint $constraint,
        DateTimeInterface $date,
        TranslatorInterface $translator
    ): string {
        $locale = $translator->getLocale();

        $constraintMin = $constraint->getMin();
        $constraintMax = $constraint->getMax();

        $min = CarbonInterval::instance($constraintMin);
        $min->locale($locale);
        $minString = $min->forHumans(['join' => true]);

        if ($constraintMax === null) {
            return $translator->trans(
                'There must be at least %interval% between password changes.',
                ['%interval%' => $minString]
            );
        }

        $max = CarbonInterval::instance($constraintMax);
        $max->locale($locale);
        $maxString = $max->forHumans(['join' => true]);

        if (Interval::compare(new DateInterval('PT0S'), $constraintMin) === 0) {
            return $translator->trans(
                'There must be at most %interval% between password changes.',
                ['%interval%' => $maxString]
            );
        }

        if (Interval::compare($constraintMin, $constraintMax) === 0) {
            return $translator->trans(
                'There must be exactly %interval% between password changes.',
                ['%interval%' => $minString]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% between password changes.',
            ['%min%' => $minString, '%max%' => $maxString]
        );
    }
}
