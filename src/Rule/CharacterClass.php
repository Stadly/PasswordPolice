<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;

class CharacterClass implements RuleInterface
{
    /**
     * @var string Characters matched by the rule.
     */
    private $characters;

    /**
     * @var int Minimum number of characters matching the rule.
     */
    private $min;

    /**
     * @var int|null Maximum number of characters matching the rule.
     */
    private $max;

    /**
     * @param string $characters Characters matched by the rule.
     * @param int $min Minimum number of characters matching the rule.
     * @param int|null $max Maximum number of characters matching the rule.
     */
    public function __construct(string $characters, int $min = 1, ?int $max = null)
    {
        if ($characters === '') {
            throw new InvalidArgumentException('At least one character must be specified.');
        }
        if ($min < 0) {
            throw new InvalidArgumentException('Min cannot be negative.');
        }
        if ($max !== null && $max < $min) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }
        if ($min === 0 && $max === null) {
            throw new InvalidArgumentException('Min cannot be zero when max is unconstrained.');
        }

        $this->characters = $characters;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return string Characters matched by the rule.
     */
    public function getCharacters(): string
    {
        return $this->characters;
    }

    /**
     * @return int Minimum number of characters matching the rule.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int|null Maximum number of characters matching the rule.
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
     * @return int Number of characters matching the rule.
     */
    private function getCount(string $password): int
    {
        $escapedCharacters = preg_quote($this->characters);
        $count = preg_match_all('{['.$escapedCharacters.']}u', $password);
        assert(false !== $count);

        return $count;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(): string
    {
        $translator = Policy::getTranslator();

        if ($this->getMax() === null) {
            return $translator->trans(
                'There must be at least one character matching %characters%.|'.
                'There must be at least %count% characters matching %characters%.',
                [
                    '%count%' => $this->getMin(),
                    '%characters%' => $this->getCharacters(),
                ]
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'There must be no characters matching %characters%.',
                ['%characters%' => $this->getCharacters()]
            );
        }

        if ($this->getMin() === 0) {
            return $translator->trans(
                'There must be at most one character matching %characters%.|'.
                'There must be at most %count% characters matching %characters%.',
                [
                    '%count%' => $this->getMax(),
                    '%characters%' => $this->getCharacters(),
                ]
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->trans(
                'There must be exactly one character matching %characters%.|'.
                'There must be exactly %count% characters matching %characters%.',
                [
                    '%count%' => $this->getMin(),
                    '%characters%' => $this->getCharacters(),
                ]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% characters matching %characters%.',
            [
                '%min%' => $this->getMin(),
                '%max%' => $this->getMax(),
                '%characters%' => $this->getCharacters(),
            ]
        );
    }
}
