<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use Symfony\Component\Translation\Translator;

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
        if ($min === 0 && $max === null) {
            throw new InvalidArgumentException('Min cannot be zero when max is unconstrained.');
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
     * Check whether a password adheres to the rule.
     *
     * @param string $password Password to check.
     * @return bool Whether the password adheres to the rule.
     */
    public function test(string $password): bool
    {
        $count = $this->getCount($password);

        if ($count < $this->min) {
            return false;
        }

        if (null !== $this->max && $this->max < $count) {
            return false;
        }

        return true;
    }

    /**
     * Enforce that a password adheres to the rule.
     *
     * @param string $password Password that must adhere to the rule.
     * @param Translator $translator For translating messages.
     * @throws RuleException If the password does not adhrere to the rule.
     */
    public function enforce(string $password, Translator $translator): void
    {
        if (!$this->test($password)) {
            throw new RuleException($this, $this->getMessage($translator));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getMessage(Translator $translator): string
    {
        if ($this->getMax() === null) {
            return $translator->transChoice(
                'There must be at least one lower case character.|'.
                'There must be at least %count% lower case characters.',
                $this->getMin()
            );
        }

        if ($this->getMax() === 0) {
            return $translator->trans(
                'There must be no lower case characters.'
            );
        }

        if ($this->getMin() === 0) {
            return $translator->transChoice(
                'There must be at most one lower case character.|'.
                'There must be at most %count% lower case characters.',
                $this->getMax()
            );
        }

        if ($this->getMin() === $this->getMax()) {
            return $translator->transChoice(
                'There must be exactly one lower case character.|'.
                'There must be exactly %count% lower case characters.',
                $this->getMin()
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% lower case characters.',
            ['%min%' => $this->getMin(), '%max%' => $this->getMax()]
        );
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
}
