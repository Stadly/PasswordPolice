<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Carbon\CarbonInterval;
use Stadly\Date\Interval;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;

final class Change implements RuleInterface
{
    /**
     * @var DateInterval|null Minimum time between password changes.
     */
    private $min;

    /**
     * @var DateInterval|null Maximum time between password changes.
     */
    private $max;

    /**
     * @param DateInterval|null $min Minimum time between password changes.
     * @param DateInterval|null $max Maximum time between password changes.
     */
    public function __construct(?DateInterval $min, ?DateInterval $max = null)
    {
        if ($min === null) {
            if ($max === null) {
                throw new InvalidArgumentException('Both min and max cannot be unconstrained.');
            }
            if (0 < Interval::compare(new DateInterval('PT0S'), $max)) {
                throw new InvalidArgumentException('Max cannot be negative.');
            }
        } else {
            if (0 < Interval::compare(new DateInterval('PT0S'), $min)) {
                throw new InvalidArgumentException('Min cannot be negative.');
            }
            if ($max !== null && 0 < Interval::compare($min, $max)) {
                throw new InvalidArgumentException('Max cannot be smaller than min.');
            }
        }

        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return DateInterval|null Minimum time between password changes.
     */
    public function getMin(): ?DateInterval
    {
        return $this->min;
    }

    /**
     * @return DateInterval|null Maximum time between password changes.
     */
    public function getMax(): ?DateInterval
    {
        return $this->max;
    }

    /**
     * Check whether a password is in compliance with the rule.
     *
     * @param Password|string $password Password to check.
     * @return bool Whether the password is in compliance with the rule.
     */
    public function test($password): bool
    {
        $date = $this->getNoncompliantDate($password);

        return $date === null;
    }

    /**
     * Enforce that a password is in compliance with the rule.
     *
     * @param Password|string $password Password that must adhere to the rule.
     * @throws RuleException If the password does not adhrere to the rule.
     */
    public function enforce($password): void
    {
        $date = $this->getNoncompliantDate($password);

        if ($date !== null) {
            throw new RuleException($this, $this->getMessage());
        }
    }

    /**
     * @param Password|string $password Password to check when was last changed.
     * @return DateTimeInterface|null When the password was last changed if not in compliance with the rule.
     */
    private function getNoncompliantDate($password): ?DateTimeInterface
    {
        $date = $this->getDate($password);

        if ($date !== null) {
            $now = new DateTimeImmutable();
            if ($this->min !== null && $now->sub($this->min) < $date) {
                return $date;
            }

            if ($this->max !== null && $date < $now->sub($this->max)) {
                return $date;
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
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        $translator = Policy::getTranslator();
        $locale = $translator->getLocale();

        $min = $this->min === null ? null : CarbonInterval::instance($this->min)->locale($locale);
        $max = $this->max === null ? null : CarbonInterval::instance($this->max)->locale($locale);

        if ($this->max === null) {
            return $translator->trans(
                'Must be at least %interval% between password changes.',
                ['%interval%' => $min]
            );
        }

        if ($this->min === null) {
            return $translator->trans(
                'Must be at most %interval% between password changes.',
                ['%interval%' => $max]
            );
        }

        if (Interval::compare($this->min, $this->max) === 0) {
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
