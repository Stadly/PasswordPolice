<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;

final class LowerCase implements RuleInterface
{
    /**
     * @var int Minimum number of lower case letters.
     */
    private $min;

    /**
     * @var int|null Maximum number of lower case letters.
     */
    private $max;

    /**
     * @param int $min Minimum number of lower case letters.
     * @param int|null $max Maximum number of lower case letters.
     */
    public function __construct(int $min = 1, ?int $max = null)
    {
        if ($min < 0) {
            throw new InvalidArgumentException('Min cannot be negative.');
        }
        if ($max !== null && $max < $min) {
            throw new InvalidArgumentException('Max cannot be smaller than min.');
        }

        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return int Minimum number of lower case letters.
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @return int|null Maximum number of lower case letters.
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
     * @return int Number of lower case characters if not in compliance with the rule.
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
     * @return int Number of lower case characters.
     */
    private function getCount(string $password): int
    {
        $upperCase = mb_strtoupper($password);

        $passwordCharacters = $this->splitString($password);
        $upperCaseCharacters = $this->splitString($upperCase);
        assert(count($passwordCharacters) === count($upperCaseCharacters));

        $count = 0;
        for ($i = count($passwordCharacters)-1; $i >= 0; --$i) {
            if ($passwordCharacters[$i] !== $upperCaseCharacters[$i]) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @param string $string String to split into individual characters.
     * @return string[] Array of characters.
     */
    private function splitString(string $string): array
    {
        $characters = preg_split('{}u', $string, -1, PREG_SPLIT_NO_EMPTY);
        assert($characters !== false);

        return $characters;
    }

    /**
     * @return string Message explaining the violation.
     */
    private function getMessage(): string
    {
        $translator = Policy::getTranslator();

        if ($this->max === null) {
            return $translator->trans(
                'There must be at least one lower case character.|'.
                'There must be at least %count% lower case characters.',
                ['%count%' => $this->min]
            );
        }

        if ($this->max === 0) {
            return $translator->trans(
                'There must be no lower case characters.'
            );
        }

        if ($this->min === 0) {
            return $translator->trans(
                'There must be at most one lower case character.|'.
                'There must be at most %count% lower case characters.',
                ['%count%' => $this->max]
            );
        }

        if ($this->min === $this->max) {
            return $translator->trans(
                'There must be exactly one lower case character.|'.
                'There must be exactly %count% lower case characters.',
                ['%count%' => $this->min]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% lower case characters.',
            ['%min%' => $this->min, '%max%' => $this->max]
        );
    }
}
