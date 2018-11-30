<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;

final class Length implements RuleInterface
{
    /**
     * @var int Minimum password length.
     */
    private $min;

    /**
     * @var int|null Maximum password length.
     */
    private $max;

    /**
     * @param int $min Minimum password length.
     * @param int|null $max Maximum password length.
     */
    public function __construct(int $min = 8, ?int $max = null)
    {
        if ($min < 0) {
            throw new InvalidArgumentException('Min cannot be negative.');
        }
        if ($max !== null && $max < $min) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }
        if ($min === 0 && $max === null) {
            throw new InvalidArgumentException('Min cannot be zero when max is unconstrained.');
        }

        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return int Minimum password length.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int|null Maximum password length.
     */
    public function getMax(): ?int
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
        $count = $this->getCount((string)$password);

        if ($count < $this->min) {
            return false;
        }

        if (null !== $this->max && $this->max < $count) {
            return false;
        }

        return true;
    }

    /**
     * Enforce that a password is in compliance with the rule.
     *
     * @param Password|string $password Password that must adhere to the rule.
     * @throws RuleException If the password does not adhrere to the rule.
     */
    public function enforce($password): void
    {
        if (!$this->test($password)) {
            throw new RuleException($this, $this->getMessage());
        }
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
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        $translator = Policy::getTranslator();

        if ($this->getMax() === null) {
            return $translator->trans(
                'There must be at least one character.|'.
                'There must be at least %count% characters.',
                ['%count%' => $this->getMin()]
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'There must be no characters.'
            );
        }

        if ($this->getMin() === 0) {
            return $translator->trans(
                'There must be at most one character.|'.
                'There must be at most %count% characters.',
                ['%count%' => $this->getMax()]
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->trans(
                'There must be exactly one character.|'.
                'There must be exactly %count% characters.',
                ['%count%' => $this->getMin()]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% characters.',
            ['%min%' => $this->getMin(), '%max%' => $this->getMax()]
        );
    }
}
