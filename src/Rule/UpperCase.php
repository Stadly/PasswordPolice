<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use StableSort\StableSort;
use Stadly\PasswordPolice\Constraint\Count;
use Stadly\PasswordPolice\Password;
use Stadly\PasswordPolice\Policy;

final class UpperCase implements RuleInterface
{
    /**
     * @var Count[] Rule constraints.
     */
    private $constraints;

    /**
     * @param int $min Minimum number of upper case letters.
     * @param int|null $max Maximum number of upper case letters.
     * @param int $weight Constraint weight.
     */
    public function __construct(int $min = 1, ?int $max = null, int $weight = 1)
    {
        $this->addConstraint($min, $max, $weight);
    }

    /**
     * @param int $min Minimum number of upper case letters.
     * @param int|null $max Maximum number of upper case letters.
     * @param int $weight Constraint weight.
     * @return $this
     */
    public function addConstraint(int $min = 1, ?int $max = null, int $weight = 1): self
    {
        $this->constraints[] = new Count($min, $max, $weight);

        StableSort::usort($this->constraints, function (Count $a, Count $b): int {
            return $b->getWeight() <=> $a->getWeight();
        });

        return $this;
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
        $constraint = $this->getViolation($count);

        return $constraint === null;
    }

    /**
     * Enforce that a password is in compliance with the rule.
     *
     * @param Password|string $password Password that must adhere to the rule.
     * @throws RuleException If the password does not adhrere to the rule.
     */
    public function enforce($password): void
    {
        $count = $this->getCount((string)$password);
        $constraint = $this->getViolation($count);

        if ($constraint !== null) {
            throw new RuleException($this, $this->getMessage($constraint, $count));
        }
    }

    /**
     * @param int $count Number of upper case characters.
     * @return Count|null Constraint violated by the count.
     */
    private function getViolation(int $count): ?Count
    {
        foreach ($this->constraints as $constraint) {
            if (!$constraint->test($count)) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * @param string $password Password to count characters in.
     * @return int Number of upper case characters.
     */
    private function getCount(string $password): int
    {
        $lowerCase = mb_strtolower($password);

        $passwordCharacters = $this->splitString($password);
        $lowerCaseCharacters = $this->splitString($lowerCase);
        assert(count($passwordCharacters) === count($lowerCaseCharacters));

        $count = 0;
        for ($i = count($passwordCharacters)-1; $i >= 0; --$i) {
            if ($passwordCharacters[$i] !== $lowerCaseCharacters[$i]) {
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
     * @param Count $constraint Constraint that is violated.
     * @param int $count Count that violates the constraint.
     * @return string Message explaining the violation.
     */
    private function getMessage(Count $constraint, int $count): string
    {
        $translator = Policy::getTranslator();

        if ($constraint->getMax() === null) {
            return $translator->trans(
                'There must be at least one upper case character.|'.
                'There must be at least %count% upper case characters.',
                ['%count%' => $constraint->getMin()]
            );
        }

        if ($constraint->getMax() === 0) {
            return $translator->trans(
                'There must be no upper case characters.'
            );
        }

        if ($constraint->getMin() === 0) {
            return $translator->trans(
                'There must be at most one upper case character.|'.
                'There must be at most %count% upper case characters.',
                ['%count%' => $constraint->getMax()]
            );
        }

        if ($constraint->getMin() === $constraint->getMax()) {
            return $translator->trans(
                'There must be exactly one upper case character.|'.
                'There must be exactly %count% upper case characters.',
                ['%count%' => $constraint->getMin()]
            );
        }

        return $translator->trans(
            'There must be between %min% and %max% upper case characters.',
            ['%min%' => $constraint->getMin(), '%max%' => $constraint->getMax()]
        );
    }
}
