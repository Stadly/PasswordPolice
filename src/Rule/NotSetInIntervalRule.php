<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\DateConstraint;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Rule;
use Stadly\PasswordPolice\ValidationError;
use Symfony\Contracts\Translation\LocaleAwareInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class NotSetInIntervalRule implements Rule
{
    /**
     * @var array<DateConstraint> Rule constraints.
     */
    private $constraints = [];

    /**
     * @param DateTimeInterface|null $end End of interval in which the password should not be set.
     * @param DateTimeInterface|null $start Start of interval in which the password should not be set.
     * @param int $weight Constraint weight.
     */
    public function __construct(?DateTimeInterface $end, ?DateTimeInterface $start = null, int $weight = 1)
    {
        $this->addConstraint($end, $start, $weight);
    }

    /**
     * @param DateTimeInterface|null $end End of interval in which the password should not be set.
     * @param DateTimeInterface|null $start Start of interval in which the password should not be set.
     * @param int $weight Constraint weight.
     * @return $this
     */
    public function addConstraint(?DateTimeInterface $end, ?DateTimeInterface $start = null, int $weight = 1): self
    {
        if ($end === null && $start === null) {
            throw new InvalidArgumentException('End or start must be specified.');
        }

        $this->constraints[] = new DateConstraint($start, $end, $weight);

        StableSort::usort($this->constraints, static function (DateConstraint $a, DateConstraint $b): int {
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
    public function test($password, ?int $weight = null): bool
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
     * @param DateTimeInterface|null $date When the password was set.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return DateConstraint|null Constraint violated by the date.
     */
    private function getViolation(?DateTimeInterface $date, ?int $weight = null): ?DateConstraint
    {
        if ($date === null) {
            return null;
        }

        foreach ($this->constraints as $constraint) {
            if ($weight !== null && $constraint->getWeight() < $weight) {
                continue;
            }
            if ($constraint->test($date)) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * @param Password|string $password Password to check when was set.
     * @return DateTimeInterface|null When the password was set.
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
     * @param DateConstraint $constraint Constraint that is violated.
     * @param DateTimeInterface $date Date that violates the constraint.
     * @param TranslatorInterface&LocaleAwareInterface $translator Translator for translating messages.
     * @return string Message explaining the violation.
     */
    private function getMessage(
        DateConstraint $constraint,
        DateTimeInterface $date,
        TranslatorInterface $translator
    ): string {
        $constraintMin = $constraint->getMin();
        $constraintMax = $constraint->getMax();

        $minString = $constraintMin === null ? '' : $constraintMin->format('Y-m-d H:i:s');
        $maxString = $constraintMax === null ? '' : $constraintMax->format('Y-m-d H:i:s');

        if ($constraintMax === null) {
            return $translator->trans(
                'The password must have been set before %date%.',
                ['%date%' => $minString]
            );
        }

        if ($constraintMin === null) {
            return $translator->trans(
                'The password must have been set after %date%.',
                ['%date%' => $maxString]
            );
        }

        if ($constraintMin->format(DateTime::RFC3339_EXTENDED)
        === $constraintMax->format(DateTime::RFC3339_EXTENDED)
        ) {
            return $translator->trans(
                'The password must have been set before or after %date%.',
                ['%date%' => $minString]
            );
        }

        return $translator->trans(
            'The password must have been set before %min% or after %max%.',
            ['%min%' => $minString, '%max%' => $maxString]
        );
    }
}
