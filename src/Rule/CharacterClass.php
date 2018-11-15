<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use Stadly\PasswordPolice\Rule;
use Symfony\Component\Translation\Translator;

class CharacterClass implements Rule
{
    /**
     * @var string Characters matching the rule.
     */
    private $characters;

    /**
     * @var int Minimum number of characters matching the rule in password.
     */
    private $min;

    /**
     * @var int|null Maximum number of characters matching the rule in password.
     */
    private $max;

    public function __construct(string $characters, int $min, ?int $max = null)
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

    public function getCharacters(): string
    {
        return $this->characters;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function test(string $password): bool
    {
        if ($this->getCount($password) < $this->min) {
            return false;
        }

        if (null !== $this->max && $this->max < $this->getCount($password)) {
            return false;
        }

        return true;
    }

    /**
     * @throws RuleException If the rule cannot be enforced.
     */
    public function enforce(string $password, Translator $translator): void
    {
        if (!$this->test($password)) {
            throw new RuleException($this, $this->getMessage($translator));
        }
    }

    public function getMessage(Translator $translator): string
    {
        if ($this->getMax() === null) {
            return $translator->transChoice(
                'There must be at least one character matching %characters%.|'.
                'There must be at least %count% characters matching %characters%.',
                $this->getMin(),
                ['%characters%' => $this->getCharacters()]
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'There must be no characters matching %characters%.',
                ['%characters%' => $this->getCharacters()]
            );
        }

        if ($this->getMin() === 0) {
            return $translator->transChoice(
                'There must be at most one character matching %characters%.|'.
                'There must be at most %count% characters matching %characters%.',
                $this->getMax(),
                ['%characters%' => $this->getCharacters()]
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->transChoice(
                'There must be exactly one character matching %characters%.|'.
                'There must be exactly %count% characters matching %characters%.',
                $this->getMin(),
                ['%characters%' => $this->getCharacters()]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% characters matching %characters%.',
            [
                '%min%' => $this->getMin(),
                '%max%' => $this->getMax(),
                '%characters%' => $this->getCharacters()
            ]
        );
    }

    private function getCount(string $password): int
    {
        $escapedCharacters = preg_quote($this->characters);
        $count = preg_match_all('{['.$escapedCharacters.']}u', $password);
        assert(false !== $count);

        return $count;
    }
}