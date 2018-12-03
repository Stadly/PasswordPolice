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
        $count = $this->getNoncompliantCount((string)$password);

        return $count === null;
    }

    /**
     * Enforce that a password is in compliance with the rule.
     *
     * @param Password|string $password Password that must adhere to the rule.
     * @throws RuleException If the password does not adhrere to the rule.
     */
    public function enforce($password): void
    {
        $count = $this->getNoncompliantCount((string)$password);

        if ($count !== null) {
            throw new RuleException($this, $this->getMessage());
        }
    }

    /**
     * @param string $password Password to count characters in.
     * @return int Number of characters if not in compliance with the rule.
     */
    private function getNoncompliantCount(string $password): ?int
    {
        $count = $this->getCount($password);

        if ($count < $this->min) {
            return $count;
        }

        if (null !== $this->max && $this->max < $count) {
            return $count;
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
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        $translator = Policy::getTranslator();

        if ($this->max === null) {
            return $translator->trans(
                'There must be at least one character.|'.
                'There must be at least %count% characters.',
                ['%count%' => $this->min]
            );
        }

        if ($this->max === 0) {
            return $translator->trans(
                'There must be no characters.'
            );
        }

        if ($this->min === 0) {
            return $translator->trans(
                'There must be at most one character.|'.
                'There must be at most %count% characters.',
                ['%count%' => $this->max]
            );
        }

        if ($this->min === $this->max) {
            return $translator->trans(
                'There must be exactly one character.|'.
                'There must be exactly %count% characters.',
                ['%count%' => $this->min]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% characters.',
            ['%min%' => $this->min, '%max%' => $this->max]
        );
    }
}
